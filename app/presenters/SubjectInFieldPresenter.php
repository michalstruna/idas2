<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:07
 */

namespace App\Presenters;


use App\Model\CategoryModel;
use App\Model\StudyFieldModel;
use App\Model\SubjectInFieldModel;
use App\Model\SubjectModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class SubjectInFieldPresenter extends BasePresenter {

    private $subjectInFieldModel;

    private $subjectModel;

    private $studyFieldModel;

    private $categoryModel;

    /**
     * SubjectInFieldPresenter constructor.
     * @param $subjectInFieldModel
     * @param $subjectModel
     * @param $studyFieldModel
     * @param $categoryModel
     */
    public function __construct(SubjectInFieldModel $subjectInFieldModel, SubjectModel $subjectModel,
                                StudyFieldModel $studyFieldModel, CategoryModel $categoryModel) {
        parent::__construct();
        $this->subjectInFieldModel = $subjectInFieldModel;
        $this->subjectModel = $subjectModel;
        $this->studyFieldModel = $studyFieldModel;
        $this->categoryModel = $categoryModel;
    }

    /**
     * Create edit subject in field form.
     * @return Form Edit subject in field form
     */
    protected function createComponentEditSubjectInFieldForm(): Form {
        $subjectInFieldId = $this->getParameter('id');
        $subjectInField = isset($subjectInFieldId) ? $this->subjectInFieldModel->getById($subjectInFieldId) : null;
        $categories = $this->categoryModel->getAll();
        $studyFields = $this->studyFieldModel->getAll();
        $subjects = $this->subjectModel->getAll();

        $form = new Form;

        $form->addSelect('studyField', 'Způsob zakončení', array_reduce($studyFields, function ($result, $studyField) {
            $result[$studyField['id']] = $studyField['nazev'];
            return $result;
        }))
            ->setDefaultValue($subjectInField['obor_id'])
            ->setRequired("Prosím vyplňte obor");

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
            'Způsoby výuky předmětu' => 'CourseTypeInField:',
            'Učí' => 'Teaching:'
        ];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add subject in field form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if (empty($this->getParameter('id'))) {
                $this->subjectInFieldModel->insert($form->getValues(true));
                $this->flashMessage('Předmět v oboru byl přidán.', self::$SUCCESS);
            } else {
                $this->subjectInFieldModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Předmět v oboru byl upraven.', self::$SUCCESS);
            }

            $this->redirect('SubjectInField:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete subject in field by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->subjectInFieldModel->deleteById($id);
            $this->flashMessage('Předmět v oboru byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('SubjectInField:');
    }

}