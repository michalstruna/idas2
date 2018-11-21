<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 13:25
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {

    public function beforeRender() {
        parent::beforeRender();

        $this->template->menuItems = [
            'Domů' => 'Homepage:',
            'Vyučující' => 'Teacher:',
            'Pracoviště' => 'Workplace:',
            'Předměty' => 'Subject:',
            'Obory' => 'StudyField:',
            'Plány' => 'StudyPlan:',
            'Rozvrhy' => 'Schedule:'
        ];

        if ($this->user->isLoggedIn()) {
            $this->template->menuItems['Odhlásit'] = 'Sign:out';
        } else {
            $this->template->menuItems['Přihlášení'] = 'Sign:in';
        }
    }

}
