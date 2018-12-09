<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 21:47
 */

namespace App\Presenters;

use App\Control\ScheduleControl;
use App\Model\ScheduleModel;
use App\Model\RoomModel;
use App\Model\CourseTypeInPlanModel;
use App\Constants\Days;
use App\Model\SemesterModel;
use App\Model\StudyPlanModel;
use App\Model\TeacherModel;
use App\Model\TeachingModel;
use App\Utils\Time;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;
use Nette\InvalidStateException;

class SchedulePresenter extends BasePresenter {

    const DEFAULT_HOUR = 8;
    const START_HOUR = 0;
    const END_HOUR = 23;

    private $scheduleModel;
    private $roomModel;
    private $courseTypeModel;
    private $teachingModel;
    private $teacherModel;
    private $semesterModel;
    private $studyPlanModel;

    public function __construct(ScheduleModel $scheduleModel, RoomModel $roomModel, CourseTypeInPlanModel $courseTypeModel, TeachingModel $teachingModel, TeacherModel $teacherModel, SemesterModel $semesterModel, StudyPlanModel $studyPlanModel) {
        parent::__construct();
        $this->scheduleModel = $scheduleModel;
        $this->roomModel = $roomModel;
        $this->courseTypeModel = $courseTypeModel;
        $this->teachingModel = $teachingModel;
        $this->teacherModel = $teacherModel;
        $this->semesterModel = $semesterModel;
        $this->studyPlanModel = $studyPlanModel;
    }

    public function createComponentFilterScheduleForm(): Form {
        $teachers = $this->teacherModel->getAll();
        $rooms = $this->roomModel->getAll();
        $semesters = $this->semesterModel->getAll();
        $studyPlans = $this->studyPlanModel->getAll();

        $form = new Form;

        $form->addSelect('teacher', null, array_reduce($teachers, function ($result, $teacher) {
            $result[$teacher['id']] = $teacher['jmeno'] . ' ' . $teacher['prijmeni'];
            return $result;
        }))
            ->setPrompt('Všichni vyučující')
            ->setDefaultValue($this->getHttpRequest()->getQuery('teacher'));

        $form->addSelect('room', null, array_reduce($rooms, function ($result, $room) {
            $result[$room['id']] = $room['nazev'];
            return $result;
        }))
            ->setPrompt('Všechny místnosti')
            ->setDefaultValue($this->getHttpRequest()->getQuery('room'));

        $form->addSelect('semester', null, array_reduce($semesters, function ($result, $semester) {
            $result[$semester['id']] = $semester['nazev'];
            return $result;
        }))
            ->setPrompt('Všechny semestry')
            ->setDefaultValue($this->getHttpRequest()->getQuery('semester'));

        $form->addSelect('plan', null, array_reduce($studyPlans, function ($result, $plan) {
            $result[$plan['id']] = $plan['nazev'];
            return $result;
        }))
            ->setPrompt('Všechny plány')
            ->setDefaultValue($this->getHttpRequest()->getQuery('plan'));

        $form->addSelect('year', null, array_reduce(range(1, 3), function ($result, $year) {
            $result[$year] = $year;
            return $result;
        }))
            ->setPrompt('Všechny ročníky')
            ->setDefaultValue($this->getHttpRequest()->getQuery('year'));

        if ($this->getUser()->isInRole('admin') || $this->getUser()->isInRole('teacher')) {
            $form->addSelect('approved', null, [true => 'Schváleno', false => 'Neschváleno'])
                ->setPrompt('Všechny stavy')
                ->setDefaultValue($this->getHttpRequest()->getQuery('approved'));
        }

        $form->addSubmit('send', 'Vyhledat');

        $form->onSuccess[] = [$this, 'onFilter'];

        return $form;
    }

    public function createComponentEditScheduleForm(): Form {
        $scheduleActionId = $this->getParameter('id');
        $scheduleAction = isset($scheduleActionId) ? $this->scheduleModel->getById($scheduleActionId) : null;
        $hours = range(self::START_HOUR, self::END_HOUR);
        $rooms = $this->roomModel->getAll();
        $courseTypes = $this->courseTypeModel->getAll();
        $teachings = $this->teachingModel->getAll();

        $selectedPlanId = null;
        if ($scheduleAction !== null) {
            foreach ($teachings as $teaching) {
                if ($teaching['id'] === $scheduleAction['uci_id']) {
                    $selectedPlanId = $teaching['predm_plan_id'];
                    break;
                }
            }
        } else if (!empty($teachings)) {
            $selectedPlanId = $teachings[0]['predm_plan_id'];
        }

        $teachings = array_filter($teachings, function ($item) use ($selectedPlanId) {
            return $item['predm_plan_id'] == $selectedPlanId;
        });

        $form = new Form;

        $form->addSelect('day', 'Den', array_reduce(Days::toArray(), function ($result, $day) {
            $result[$day['index']] = $day['text'];
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['den_v_tydnu'] : null)
            ->setRequired('Prosím vyberte den');

        $form->addSelect('start', 'Začátek', array_reduce($hours, function ($result, $hour) {
            $result[$hour] = ($hour < 10 ? '0' : '') . $hour . '.00';
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['zacatek'] : self::DEFAULT_HOUR)
            ->setRequired('Prosím vyberte hodinu');

        $form->addSelect('courseType', 'Způsob předmětu', array_reduce($courseTypes, function ($result, $courseType) {
            $result[$courseType['id']] = $courseType['zpusob_vyuky'] . ', ' . $courseType['predm_plan'] . ', ' . $courseType['pocet_hodin'] . ' h, ' . $courseType['kapacita'] . ' studentů';
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['zpusob_zakonceni_predmetu_id'] : null)
            ->setRequired('Prosím vyberte způsob předmětu');

        $form->addSelect('teaching', 'Vyučující', array_reduce($teachings, function ($result, $teaching) {
            $result[$teaching['id']] = $teaching['ucitel'] . ', ' . $teaching['role'] . ', ' . $teaching['predmet'];
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['uci_id'] : null)
            ->setRequired('Prosím vyberte vyučujícího');

        $form->addSelect('room', 'Místnost', array_reduce($rooms, function ($result, $room) {
            $result[$room['id']] = $room['nazev'] . ' (' . $room['kapacita'] . ')';
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['mistnost_id'] : null)
            ->setRequired('Prosím vyberte místnost');

        $form->addText('date', 'Přesný datum')
            ->setType('date')
            ->setDefaultValue($scheduleAction ? $scheduleAction['datum'] : '');


        if ($this->getUser()->isInRole('admin')) {
            $form->addCheckbox('approved', 'Schváleno')
                ->setDefaultValue($scheduleAction ? $scheduleAction['schvaleno'] : false);
        } else {
            $form->addHidden('approved', $scheduleAction ? $scheduleAction['schvaleno'] : '0');
        }

        $form->addSubmit('send', $scheduleAction ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];

        return $form;
    }

    public function onFilter(Form $form, array $values): void {
        $this->redirect('Schedule:', [
            'teacherId' => $values['teacher'],
            'roomId' => $values['room'],
            'semesterId' => $values['semester'],
            'planId' => $values['plan'],
            'yearId' => $values['year'],
            'approved' => $values['approved']
        ]);
    }

    /**
     * Handler for edit or add completion type form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->scheduleModel->insert($form->getValues(true));
                $this->flashMessage('Rozvrhová akce byla přidána.', self::$SUCCESS);
            } else {
                $this->scheduleModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Rozvrhová akce byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Schedule:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        } catch (InvalidStateException $exception) {
            $this->flashMessage($exception->getMessage(), self::$ERROR);
        }
    }

    public function renderDefault(): void {
        $this->template->scheduleActions = $this->scheduleModel->getByFilter([
            '"ucitel_id"' => $this->getHttpRequest()->getQuery('teacher'),
            '"mistnost_id"' => $this->getHttpRequest()->getQuery('room'),
            '"semestr_id"' => $this->getHttpRequest()->getQuery('semester'),
            '"plan_id"' => $this->getHttpRequest()->getQuery('plan'),
            '"rocnik"' => $this->getHttpRequest()->getQuery('year'),
            'schvaleno' => $this->getHttpRequest()->getQuery('approved')
        ]);

        $this->template->tabs = [];
        $this->template->hoursSum = array_sum(array_map(function ($action) {
            return $action['pocet_hodin'];
        }, $this->template->scheduleActions));

        $this->template->getDayNameByIndex = function ($index): string {
            return Days::findByNestedKey('index', (int)$index)['text'];
        };

        $this->template->formatInterval = function ($start, $item): string {
            return Time::formatInterval($start, $item['pocet_hodin']);
        };
    }

    private function requireTeacherOwnerIfUnapproved(string $id): void {
        $scheduleAction = $this->scheduleModel->getById($id);
        $teaching = $this->teachingModel->getById($scheduleAction['uci_id']);

        if (
            !$this->getUser()->isInRole('admin') &&
            (!$this->getUser()->isInRole('teacher') || $this->getUser()->identity->teacherId !== $teaching['ucitel_id'] || $scheduleAction['schvaleno'])
        ) {
            $this->flashMessage('Nedostatečná oprávnění!', self::$ERROR);
            $this->redirect('Schedule:');
        }
    }

    public function renderEdit(string $id): void {
        $this->requireTeacherOwnerIfUnapproved($id);
    }

    public function renderAdd(): void {
        if (!$this->getUser()->isInRole('teacher')) {
            $this->requireAdmin();
        }
    }

    /**
     * Delete schedule action by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireTeacherOwnerIfUnapproved($id);

        try {
            $this->scheduleModel->deleteById($id);
            $this->flashMessage('Rozvrhová akce byla vymazána.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Schedule:');
    }

    public function createComponentSchedule(): ScheduleControl {
        return new ScheduleControl();
    }

}