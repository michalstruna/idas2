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
use Nette\Utils\ArrayHash;

class FacultyPresenter extends BasePresenter {

    private $facultyModel;

    public function __construct(FacultyModel $facultyModel) {
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
            ->setDefaultValue($faculty ? $faculty['nazev'] : '');

        $form->addText('shortName', 'Zkratka')
            ->setRequired('Prosím vyplňte zkratku.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($faculty ? $faculty['zkratka'] : '');

        $form->addSubmit('send', $faculty ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->faculties = $this->facultyModel->getAll();
    }

    public function renderEdit(string $id): void {

    }

    public function renderAdd(): void {

    }

    /**
     * Handler for edit or add faculty form.
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form, ArrayHash $values): void {
        try {
            if(empty($this->getParameter('id'))) {
                $this->facultyModel->insert($form->getValues(true));
            } else {
                $this->facultyModel->updateById($this->getParameter('id'), $form->getValues(true));
            }
        } catch(DriverException $exception) {
            $this->flashMessage($exception->getMessage());
        }

        $this->redirect('Faculty:');
    }

    /**
     * Delete faculty by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->facultyModel->deleteById($id);
        } catch(DriverException $exception) {
            $this->flashMessage($exception->getMessage());
        }

        $this->redirect('Faculty:');
    }

}