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

        $form->addPassword('password', 'Nové heslo');

        $form->addPassword('passwordAgain', 'Nové heslo znovu');

        if($this->user->isInRole('admin')) {
            $form->addSelect('teacher', 'Učitel', array_reduce($teachers, function ($result, $teacher) {
                $result[$teacher['id']] = $teacher['dlouhe_jmeno'];
                return $result;
            }))
                ->setPrompt('Bez učitele')
                ->setDefaultValue($user['ucitel_id']);
        } else {
            $form->addHidden('teacher', $user['ucitel_id']);
        }

        $form->addSubmit('send', $user ? 'Upravit' : 'Přidat');

        $form->onSuccess[] = [$this, 'onEdit'];
        return $form;
    }

    public function renderDefault(): void {
        $this->template->users = $this->userModel->getAll();
        $this->template->tabs = [];
    }

    public function renderEdit(string $id) {
        if(!$this->isOwner()) {
            $this->requireAdmin();
        }

        $this->template->isOwner = $this->isOwner();
        $this->template->tabs = [];

        if($this->getUser()->isInRole('teacher')) {
           $this->template->tabs['Můj profil'] = ['Teacher:edit', $this->getUser()->getIdentity()->teacherId];
        }

        $this->template->tabs['Odhlásit se'] = 'Sign:out';
    }

    /**
     * Handler for edit room user.
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function onEdit(Form $form, ArrayHash $values): void {
        try {
            if(empty($this->getParameter('id'))) {

            } else {
                if($values['password'] !== $values['passwordAgain']) {
                    throw new InvalidArgumentException('Hesla se neshodují.');
                }

                $this->userModel->updateById($this->getParameter('id'), $form->getValues(true));
                //$this->getUser()->getIdentity()->teacherId = $values['teacher'];
                $this->flashMessage('Účet byl upraven.', self::$SUCCESS);
                // TODO: Reauthenticate, if user changes roles himself?

                if(!$this->isOwner()) {
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
     * ID of edited and logged user are same.
     * @return bool User is owner of this account.
     */
    private function isOwner(): bool {
        $id = $this->getParameter('id');
        return isset($id) && $this->getUser()->getId() === intval($this->getParameter('id'));
    }

}