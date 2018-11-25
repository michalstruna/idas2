<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 24/11/2018
 * Time: 16:30
 */

namespace App\Presenters;

use App\Model\FacultyModel;
use Nette\Database\DriverException;
use Nette\Application\UI\Form;

class FacultyPresenter extends BasePresenter {

    private $facultyModel;

    public function __construct(FacultyModel $facultyModel) {
        parent::__construct();
        $this->facultyModel = $facultyModel;
    }

    /**
     * Create edit faculty form.
     * @return Form Edit faculty form
     */
    protected function createComponentEditFacultyForm(): Form {
        $facultyId = $this->getParameter('id');
        $faculty = isset($facultyId) ? $this->facultyModel->getById($facultyId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($faculty ? $faculty['nazev'] : '')
            ->setMaxLength(255);

        $form->addText('shortName', 'Zkratka')
            ->setRequired('Prosím vyplňte zkratku.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($faculty ? $faculty['zkratka'] : '')
            ->setMaxLength(10);

        $form->addSubmit('send', $faculty ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->faculties = $this->facultyModel->getAll();
        $this->template->tabs = ['Katedry' => 'Department:'];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add faculty form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if(empty($this->getParameter('id'))) {
                $this->facultyModel->insert($form->getValues(true));
                $this->flashMessage('Fakulta byla přidána.', self::$SUCCESS);
            } else {
                $this->facultyModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Fakulta byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Faculty:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

    }

    /**
     * Delete faculty by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->facultyModel->deleteById($id);
            $this->flashMessage('Fakulta byla vymazána.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Faculty:');
    }

}