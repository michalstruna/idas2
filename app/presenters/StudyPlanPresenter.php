<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-27
 * Time: 22:34
 */

namespace App\Presenters;


use App\Model\StudyFieldModel;
use App\Model\StudyPlanModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class StudyPlanPresenter extends BasePresenter {

    private $studyPlanModel;

    private $studyFieldModel;

    /**
     * StudyPlanPresenter constructor.
     * @param $studyPlanModel
     * @param $studyFieldModel
     */
    public function __construct(StudyPlanModel $studyPlanModel, StudyFieldModel $studyFieldModel) {
        parent::__construct();
        $this->studyPlanModel = $studyPlanModel;
        $this->studyFieldModel = $studyFieldModel;
    }

    /**
     * Create edit study plan form.
     * @return Form Edit completion type form
     */
    protected function createComponentEditStudyPlanForm(): Form {
        $studyPlanId = $this->getParameter('id');
        $studyPlan = isset($studyPlanId) ? $this->studyPlanModel->getById($studyPlanId) : null;
        $studyFields = $this->studyFieldModel->getAll();

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($studyPlan ? $studyPlan['nazev'] : '')
            ->setMaxLength(50);

        $form->addInteger('students', 'Odhad počtu studentů')
            ->setRequired('Prosím vyplňte odhad počtu studentů.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($studyPlan ? $studyPlan['odhad_studentu'] : '');

        $form->addSelect('studyField', 'Obor', array_reduce($studyFields, function ($result, $studyField) {
            $result[$studyField['id']] = $studyField['nazev'];
            return $result;
        }))
            ->setDefaultValue($studyPlan['obor_id'])
            ->setRequired("Prosím vyplňte obor");

        $form->addSubmit('send', $studyPlan ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->studyPlans = $this->studyPlanModel->getAll();
        $this->template->tabs = [
            'Předměty ve studijním plánu' => 'SubjectInPlan:',
            'Způsoby výuky předmětu' => 'CourseTypeInPlan:',
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
     * Handler for edit or add study plan form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if(empty($this->getParameter('id'))) {
                $this->studyPlanModel->insert($form->getValues(true));
                $this->flashMessage('Studijní plán byl přidán.', self::$SUCCESS);
            } else {
                $this->studyPlanModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Studijní plán byl upraven.', self::$SUCCESS);
            }

            $this->redirect('StudyPlan:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete study plan by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->studyPlanModel->deleteById($id);
            $this->flashMessage('Studijní plán byl vymazán.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('StudyPlan:');
    }

}