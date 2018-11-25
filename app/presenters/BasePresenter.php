<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 13:25
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use App\Control\TableControl;
use App\Control\TabMenuControl;

abstract class BasePresenter extends Presenter {

    protected static $SUCCESS = 'success';
    protected static $ERROR = 'error';

    public function beforeRender() {
        parent::beforeRender();

        /**
         * Menu items. Each item is associative array TEXT => [TARGET, [...SUBPAGE_PRESENTERS]].
         */
        $this->template->menuItems = [
            'Domů' => ['Homepage:', []],
            'Vyučující' => ['Teacher:', []],
            'Pracoviště' => ['Department:', ['Faculty']],
            'Předměty' => ['Subject:', []],
            'Obory' => ['StudyField:', []],
            'Plány' => ['StudyPlan:', []],
            'Rozvrhy' => ['Schedule:', []]
        ];

        if ($this->user->isLoggedIn()) {
            $this->template->menuItems['Odhlásit'] = ['Sign:out', []];
        } else {
            $this->template->menuItems['Přihlášení'] = ['Sign:in', []];
        }
    }

    protected function createComponentTable() {
        return new TableControl();
    }

    protected function createComponentTabMenu() {
        return new TabMenuControl();
    }

}