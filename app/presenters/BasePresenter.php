<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {

    public function beforeRender() {
        parent::beforeRender();

        $this->template->menuItems = [
            'Domů' => 'Homepage:',
            'Přihlášení' => 'Sign:in'
        ];
    }

}
