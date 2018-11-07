<?php

namespace App\Presenters;

use Nette;


final class AuthenticationPresenter extends Nette\Application\UI\Presenter {

    public function renderLogin() {
        $this->template->idParameter = $this->getParameter('id');
    }

}
