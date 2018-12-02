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
use App\Model\TeachingModel;
use App\Utils\Time;
use Nette\Application\UI\Form;

class SchedulePresenter extends BasePresenter {

    const DEFAULT_HOUR = 8;
    const START_HOUR = 0;
    const END_HOUR = 23;

    private $scheduleModel;
    private $roomModel;
    private $courseTypeModel;
    private $teachingModel;

    public function __construct(ScheduleModel $scheduleModel, RoomModel $roomModel, CourseTypeInPlanModel $courseTypeModel, TeachingModel $teachingModel) {
        $this->scheduleModel = $scheduleModel;
        $this->roomModel = $roomModel;
        $this->courseTypeModel = $courseTypeModel;
        $this->teachingModel = $teachingModel;
    }

    public function createComponentEditScheduleForm(): Form {
        $scheduleActionId = $this->getParameter('id');
        $scheduleAction = isset($scheduleActionId) ? $this->scheduleModel->getById($scheduleActionId) : null;
        $hours = range(self::START_HOUR, self::END_HOUR);
        $rooms = $this->roomModel->getAll();
        $courseTypes = $this->courseTypeModel->getAll();
        $teachings = $this->teachingModel->getAll();

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

        $form->addSelect('room', 'Místnost', array_reduce($rooms, function ($result, $room) {
            $result[$room['id']] = $room['nazev'] . ' (' . $room['kapacita'] . ')';
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['mistnost_id'] : null)
            ->setRequired('Prosím vyberte místnost');

        $form->addSelect('courseType', 'Způsob předmětu', array_reduce($courseTypes, function ($result, $courseType) {
            $result[$courseType['id']] = $courseType['zpusob_vyuky'] . ', ' . $courseType['predm_plan'] . ', ' . $courseType['pocet_hodin'] . ' h, ' . $courseType['kapacita'] . ' studentů';
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['zpusob_zakonceni_predmetu_id'] : null)
            ->setRequired('Prosím vyberte způsob předmětu');

        $form->addSelect('teaching', 'Výuka', array_reduce($teachings, function ($result, $teaching) {
            $result[$teaching['id']] = $teaching['ucitel'] . ', ' . $teaching['role'] . ', ' . $teaching['predmet'];
            return $result;
        }))
            ->setDefaultValue($scheduleAction ? $scheduleAction['uci_id'] : null)
            ->setRequired('Prosím vyberte výuku');

        $form->addText('date', 'Přesný datum')
            ->setType('date')
            ->setDefaultValue($scheduleAction ? $scheduleAction['datum'] : '');


        $form->addSubmit('send', $scheduleAction ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
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
        }
    }

    public function renderDefault(): void {
        $this->template->scheduleActions = $this->scheduleModel->getAll();
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

    public function renderEdit(string $id): void {

    }

    public function renderAdd(): void {

    }

    /**
     * Delete schedule action by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin(); // TODO: Or owner teacher.
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