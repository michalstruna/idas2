<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 26/11/2018
 * Time: 09.49
 */

namespace App\Presenters;

use Nette\InvalidArgumentException;
use Nette\Application\UI\Form;
use Nette\Database\DriverException;
use App\Model\UserModel;
use App\Model\TeacherModel;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

class UserPresenter extends BasePresenter {

    private $userModel;
    private $teacherModel;

    public function __construct(UserModel $userModel, TeacherModel $teacherModel) {
        parent::__construct();
        $this->userModel = $userModel;
        $this->teacherModel = $teacherModel;
    }

    /**
     * Create edit user form.
     * @return Form Edit user form
     */
    protected function createComponentEditUserForm(): Form {
        $userId = $this->getParameter('id');
        $user = isset($userId) ? $this->userModel->getById($userId) : null;
        $teachers = $this->teacherModel->getAll();

        $form = new Form;
        $form->addEmail('email', 'Email')
            ->setRequired('Prosím vyplňte email.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setDefaultValue($user ? $user['email'] : '')
            ->setMaxLength(255);

        $pass1 = $form->addPassword('password', $user ? 'Nové heslo' : 'Heslo');

        $pass2 = $form->addPassword('passwordAgain', $user ? 'Nové heslo znovu' : 'Heslo znovu');

        if ($user === null) {
            $pass1->setRequired('Prosím vyplňte heslo');
            $pass2->setRequired('Prosím vyplňte heslo znovu');
        }

        if ($this->user->isInRole('admin')) {
            $form->addSelect('teacher', 'Učitel', array_reduce($teachers, function ($result, $teacher) {
                $result[$teacher['id']] = $teacher['dlouhe_jmeno'];
                return $result;
            }))
                ->setPrompt('Bez učitele')
                ->setDefaultValue($user['ucitel_id']);

            $form->addCheckbox('admin', 'Uživatel je admin')
                ->setDefaultValue($user ? $user['admin'] === '1' : false);
        } else {
            $form->addHidden('teacher', $user['ucitel_id']);
            $form->addHidden('admin', $user['admin']);
        }

        $form->addSubmit('send', $user ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->requireAdmin();
        $this->template->users = $this->userModel->getAll();
        $this->template->tabs = [];
    }

    public function renderEdit(string $id) {
        if (!$this->isOwner()) {
            $this->requireAdmin();
        }

        $this->template->isOwner = $this->isOwner();
        $this->template->tabs = [];

        if ($this->getUser()->isInRole('teacher')) {
            $this->template->tabs['Můj profil učitele'] = ['Teacher:edit', $this->getUser()->getIdentity()->teacherId];
        }

        $this->template->tabs['Odhlásit se'] = 'Sign:out';
    }

    public function renderAdd(): void {
        $this->requireAdmin();
    }

    /**
     * Handler for edit user.
     * @param Form $form
     * @param ArrayHash $values
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form, ArrayHash $values): void {
        try {
            if (empty($this->getParameter('id'))) {
                $this->requireAdmin();

                if ($values['password'] !== $values['passwordAgain']) {
                    throw new InvalidArgumentException('Hesla se neshodují.');
                }

                $this->userModel->insert($form->getValues(true));
                $this->flashMessage('Uživatel byl přidán.', self::$SUCCESS);
            } else {
                if (!$this->isOwner()) {
                    $this->requireAdmin();
                }

                if ($values['password'] !== $values['passwordAgain']) {
                    throw new InvalidArgumentException('Hesla se neshodují.');
                }

                $this->userModel->updateById($this->getParameter('id'), $form->getValues(true));
                $this->flashMessage('Účet byl upraven.', self::$SUCCESS);


                $this->handleReAuthentication();

                if ($this->user->isInRole('admin')) {
                    $this->redirect('User:');
                }
            }
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        } catch (InvalidArgumentException $exception) {
            $this->flashMessage($exception->getMessage(), self::$ERROR);
        }
    }

    /**
     * Delete user by ID.
     * @param string $id
     * @throws \Nette\Application\AbortException
     */
    public function actionDelete(string $id): void {
        $this->requireAdmin();
        try {
            $this->userModel->deleteById($id);
            $this->handleReAuthentication();
            $this->flashMessage('Uživatel byl vymazán.', self::$SUCCESS);
        } catch (DriverException $exception) {
            $this->showErrorMessage($exception);
        }

        $this->redirect('Room:');
    }

    /**
     * ID of edited and logged user are same.
     * @return bool User is owner of this account.
     */
    private function isOwner(): bool {
        $id = $this->getParameter('id');
        return isset($id) && $this->getUser()->getId() === intval($this->getParameter('id'));
    }

    private function handleReAuthentication() {
        if ($this->isOwner()) {
            try {
                $this->user->login($this->userModel->authenticateById($this->getParameter('id')));
            } catch (AuthenticationException $exception) {
                $this->redirect('SIgn:out');
                $this->flashMessage($exception->getMessage(), self::$ERROR);
            }
        }
    }

}