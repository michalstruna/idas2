<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 21:48
 */

namespace App\Presenters;


use App\Model\RoleModel;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;

class RolePresenter extends BasePresenter {

    private $roleModel;

    /**
     * RolePresenter constructor.
     * @param $roleModel
     */
    public function __construct(RoleModel $roleModel) {
        parent::__construct();
        $this->roleModel = $roleModel;
    }

    /**
     * Create edit role form.
     * @return Form Edit role form
     */
    protected function createComponentEditRoleForm(): Form {
        $roleId = $this->getParameter('id');
        $role = isset($roleId) ? $this->roleModel->getById($roleId) : null;

        $form = new Form;
        $form->addText('name', 'Název')
            ->setRequired('Prosím vyplňte název.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($role ? $role['nazev'] : '')
            ->setMaxLength(50);

        $form->addSubmit('send', $role ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->roles = $this->roleModel->getAll();
        $this->template->tabs = ['Vyučující' => 'Teacher:'];
    }

    public function renderEdit(string $id): void {
        $this->requireAdmin();
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit or add role form.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form): void {
        try {
            if(empty($this->getParameter('id'))) {
                $this->roleModel->insert($form->getValues(true));
                $this->flashMessage('Role byla přidána.', self::$SUCCESS);
            } else {
                $this->roleModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Role byla upravena.', self::$SUCCESS);
            }

            $this->redirect('Role:');
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }
    }

    /**
     * Delete role by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        try {
            $this->roleModel->deleteById($id);
            $this->flashMessage('Role byla vymazána.', self::$SUCCESS);
        } catch(DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Role:');
    }

}