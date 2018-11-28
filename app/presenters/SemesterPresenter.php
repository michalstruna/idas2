<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-28
 * Time: 10:37
 */

namespace App\Presenters;


use App\Model\SemesterModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class SemesterPresenter extends BasePresenter {

    private $semesterModel;

    /**
     * SemesterPresenter constructor.
     * @param $semesterModel
     */
    public function __construct(SemesterModel $semesterModel) {
        parent::__construct();
        $this->semesterModel = $semesterModel;
    }

    /**
     * Create edit semester form.
     * @return Form Edit semester form
     */
    protected function createComponentEditSemesterForm(): Form {
        $semesterId = $this->getParameter('id');
        $semester = isset($semesterId) ? $this->semesterModel->getById($semesterId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($semester ? $semester['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $semester ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->semesters = $this->semesterModel->getAll();
        $this->template->tabs = [
            'Studijní plány' => 'StudyPlan:',
            'Předměty ve studijním plánu' => 'SubjectInPlan:',
            'Způsoby výuky předmětu' => 'CourseTypeInPlan:',
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
     * Handler for edit or add semester form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        $this->requireAdmin();
        try {
            if (empty($this->getParameter('id'))) {
                $this->semesterModel->insert($form->getValues(true));
                $this->flashMessage('Semestr byl přidána.', self::$SUCCESS);
            } else {
                $this->semesterModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Semestr byl upraven.', self::$SUCCESS);
            }

            $this->redirect('Semester:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete semester by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->semesterModel->deleteById($id);
            $this->flashMessage('Semestr byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Semester:');
    }

}