<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 23:12
 */

namespace App\Presenters;


use App\Model\CourseTypeInPlanModel;
use App\Model\CourseTypeModel;
use App\Model\SubjectInPlanModel;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class CourseTypeInPlanPresenter extends BasePresenter {

    private $courseTypeInPlanModel;

    private $subjectInFieldModel;

    private $courseTypeModel;

    /**
     * CourseTypeInFieldPresenter constructor.
     * @param CourseTypeInPlanModel $courseTypeInPlanModel
     * @param SubjectInPlanModel $subjectInFieldModel
     * @param CourseTypeModel $courseTypeModel
     */
    public function __construct(CourseTypeInPlanModel $courseTypeInPlanModel, SubjectInPlanModel $subjectInFieldModel,
                                CourseTypeModel $courseTypeModel) {
        parent::__construct();
        $this->courseTypeInPlanModel = $courseTypeInPlanModel;
        $this->subjectInFieldModel = $subjectInFieldModel;
        $this->courseTypeModel = $courseTypeModel;
    }

    /**
     * Create edit course type in plan form.
     * @return Form Edit course type in plan form
     */
    protected function createComponentEditCourseTypeInPlanForm(): Form {
        $courseTypeInPlanId = $this->getParameter('id');
        $courseTypeInPlan = isset($courseTypeInPlanId) ? $this->courseTypeInPlanModel->getById($courseTypeInPlanId) : null;
        $subjectsInPlans = $this->subjectInFieldModel->getAll();
        $courseTypes = $this->courseTypeModel->getAll();

        $form = new Form;

        $form->addSelect('subjectInPlan', 'Předmět v plánu', array_reduce($subjectsInPlans, function ($result, $subjectInPlan) {
            $result[$subjectInPlan['id']] = $subjectInPlan['plan'] . ' - ' . $subjectInPlan['predmet'];
            return $result;
        }))
            ->setDefaultValue($courseTypeInPlan['predm_plan_id'])
            ->setRequired("Prosím vyplňte předmět ve studijním plánu");

        $form->addSelect('courseType', 'Způsob výuky', array_reduce($courseTypes, function ($result, $courseType) {
            $result[$courseType['id']] = $courseType['nazev'];
            return $result;
        }))
            ->setDefaultValue($courseTypeInPlan['zpusob_vyuky_id'])
            ->setRequired("Prosím vyplňte způsob výuky");

        $form->addInteger('hours', 'Počet hodin')
            ->setRequired('Prosím vyplňte počet hodin.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($courseTypeInPlan ? $courseTypeInPlan['pocet_hodin'] : '')
            ->setMaxLength(10);

        $form->addInteger('capacity', 'Kapacita')
            ->setRequired('Prosím vyplňte kapacitu.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($courseTypeInPlan ? $courseTypeInPlan['kapacita'] : '');

        $form->addSubmit('send', $courseTypeInPlan ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->courseTypesInPlan = $this->courseTypeInPlanModel->getAll();
        $this->template->tabs = [
            'Studijní plány' => 'StudyPlan:',
            'Předměty ve studijním plánu' => 'SubjectInPlan:',
            'Učí' => 'Teaching:',
            'Semestry' => 'Semester:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add course type in field form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->courseTypeInPlanModel->insert($form->getValues(true));
                $this->flashMessage('Způsob výuky předmětu byl přidán.', self::$SUCCESS);
            } else {
                $this->courseTypeInPlanModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Způsob výuky předmětu byl upraven.', self::$SUCCESS);
            }

            $this->redirect('CourseTypeInPlan:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete course type in field by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->courseTypeInPlanModel->deleteById($id);
            $this->flashMessage('Způsob výuky předmětu byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('CourseTypeInPlan:');
    }

    public function actionJson() {
        $this->sendResponse(new JsonResponse($this->courseTypeInPlanModel->getAll()));
    }

}