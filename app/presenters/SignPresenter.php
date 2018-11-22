<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\ArrayHash;

/**
 * Class SignPresenter
 * @package App\Presenters
 */
final class SignPresenter extends BasePresenter {

    /**
     * Create sign in form.
     * @return Form Sign in form
     */
    protected function createComponentSignInForm(): Form {
        $form = new Form;
        $form->addEmail('email')
            ->setRequired('Prosím vyplňte své uživatelské jméno.')
            ->setHtmlAttribute('autocomplete', 'off')
            ->setHtmlAttribute('placeholder', 'Email');

        $form->addPassword('password')
            ->setRequired('Prosím vyplňte své heslo.')
            ->setHtmlAttribute('placeholder', 'Heslo');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'onSignInFormSuccess'];
        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     * @throws Nette\Application\AbortException
     */
    public function onSignInFormSuccess(Form $form, ArrayHash $values): void {
        try {
            $this->getUser()->login($values->email, $values->password);
            $this->redirect('Homepage:');

        } catch (AuthenticationException $exception) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }

    /**
     * Render signIn template.
     */
    public function renderIn(): void {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Homepage:');
        }
    }

    /**
     * Logout user and redirect to homepage.
     */
    public function actionOut(): void {
        // TODO: Create ACL using new Nette\Security\Permission and check $user->isAllowed('logout').
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Homepage:');
    }

}
