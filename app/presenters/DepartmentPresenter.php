<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 21:24
 */

namespace App\Presenters;

use App\Model\FacultyModel;
use App\Model\DepartmentModel;
use Nette\Database\DriverException;
use Nette\Application\UI\Form;

class DepartmentPresenter extends BasePresenter {

    private $departmentModel;
    private $facultyModel;

    public function __construct(DepartmentModel $departmentModel, FacultyModel $facultyModel) {
        parent::__construct();
        $this->departmentModel = $departmentModel;
        $this->facultyModel = $facultyModel;
    }

    /**
     * Create edit department form.
     * @return Form Edit department form
     */
    protected function createComponentEditDepartmentForm(): Form {
        $departmentId = $this->getParameter('id');
        $department = isset($departmentId) ? $this->departmentModel->getById($departmentId) : null;
        $faculties = $this->facultyModel->getAll();

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($department ? $department['nazev'] : '')
            ->setMaxLength(255);

        $form->addText('shortName', 'Zkratka')
            ->setRequired('Prosím vyplňte zkratku.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($department ? $department['zkratka'] : '')
            ->setMaxLength(10);

        $form->addSelect('faculty', 'Fakulta', array_reduce($faculties, function ($result, $faculty) {
            $result[$faculty['id']] = $faculty['zkratka'] . ' - ' . $faculty['nazev'];
            return $result;
        }))
            ->setRequired("Prosím vyplňte fakultu");

        $form->addSubmit('send', $department ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->departments = $this->departmentModel->getAll();
        $this->template->tabs = ['Fakulty' => 'Faculty:', 'Místnosti' => 'Room:'];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add department form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->departmentModel->insert($form->getValues(true));
                $this->flashMessage('Katedra byla přidána.', self::$SUCCESS);
            } else {
                $this->departmentModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Katedra byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Department:');
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete department by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->departmentModel->deleteById($id);
            $this->flashMessage('Katedra byla vymazána.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Department:');
    }

}