<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:07
 */

namespace App\Presenters;


use App\Model\CategoryModel;
use App\Model\SemesterModel;
use App\Model\StudyPlanModel;
use App\Model\SubjectInPlanModel;
use App\Model\SubjectModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class SubjectInPlanPresenter extends BasePresenter {

    private $subjectInFieldModel;

    private $subjectModel;

    private $studyPlanModel;

    private $categoryModel;

    private $semesterModel;

    /**
     * SubjectInPlanPresenter constructor.
     * @param SubjectInPlanModel $subjectInPlanModel
     * @param SubjectModel $subjectModel
     * @param StudyPlanModel $studyPlanModel
     * @param CategoryModel $categoryModel
     * @param SemesterModel $semesterModel
     */
    public function __construct(SubjectInPlanModel $subjectInPlanModel, SubjectModel $subjectModel,
                                StudyPlanModel $studyPlanModel, CategoryModel $categoryModel,
                                SemesterModel $semesterModel) {
        parent::__construct();
        $this->subjectInFieldModel = $subjectInPlanModel;
        $this->subjectModel = $subjectModel;
        $this->studyPlanModel = $studyPlanModel;
        $this->categoryModel = $categoryModel;
        $this->semesterModel = $semesterModel;
    }

    /**
     * Create edit subject in plan form.
     * @return Form Edit subject in plan form
     */
    protected function createComponentEditSubjectInPlanForm(): Form {
        $subjectInFieldId = $this->getParameter('id');
        $subjectInField = isset($subjectInFieldId) ? $this->subjectInFieldModel->getById($subjectInFieldId) : null;
        $categories = $this->categoryModel->getAll();
        $studyPlan = $this->studyPlanModel->getAll();
        $subjects = $this->subjectModel->getAll();
        $semesters = $this->semesterModel->getAll();

        $form = new Form;

        $form->addSelect('studyPlan', 'Studijní plán', array_reduce($studyPlan, function ($result, $studyPlan) {
            $result[$studyPlan['id']] = $studyPlan['nazev'];
            return $result;
        }))
            ->setDefaultValue($subjectInField['studijni_plan_id'])
            ->setRequired("Prosím vyplňte studijní plán");

        $form->addSelect('subject', 'Předmět', array_reduce($subjects, function ($result, $subject) {
            $result[$subject['id']] = $subject['nazev'];
            return $result;
        }))
            ->setDefaultValue($subjectInField['predmet_id'])
            ->setRequired("Prosím vyplňte předmět");

        $form->addSelect('category', 'Kategorie', array_reduce($categories, function ($result, $category) {
            $result[$category['id']] = $category['nazev'];
            return $result;
        }))
            ->setDefaultValue($subjectInField['kategorie_id'])
            ->setRequired("Prosím vyplňte kategorii");

        $form->addSelect('semester', 'Semestr', array_reduce($semesters, function ($result, $semester) {
            $result[$semester['id']] = $semester['nazev'];
            return $result;
        }))
            ->setDefaultValue($subjectInField['semestr_id'])
            ->setRequired("Prosím vyplňte semestr");

        $form->addInteger('year', 'Ročník')
            ->setRequired('Prosím vyplňte ročník.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($subjectInField ? $subjectInField['rocnik'] : '')
            ->setMaxLength(10);

        $form->addInteger('studentCount', 'Počet studentů')
            ->setRequired('Prosím vyplňte počet studentů.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($subjectInField ? $subjectInField['pocet_studentu'] : '');

        $form->addSubmit('send', $subjectInField ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->subjectsInField = $this->subjectInFieldModel->getAll();
        $this->template->tabs = [
            'Studijní plány' => 'StudyPlan:',
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
     * Handler for edit or add subject in plan form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if (empty($this->getParameter('id'))) {
                $this->subjectInFieldModel->insert($form->getValues(true));
                $this->flashMessage('Předmět ve studijním plánu byl přidán.', self::$SUCCESS);
            } else {
                $this->subjectInFieldModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Předmět ve studijním plánu byl upraven.', self::$SUCCESS);
            }

            $this->redirect('SubjectInPlan:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete subject in plan by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->subjectInFieldModel->deleteById($id);
            $this->flashMessage('Předmět ve studijním plánu byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('SubjectInPlan:');
    }

}