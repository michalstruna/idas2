<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 20/11/2018
 * Time: 13:25
 */

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\DriverException;
use App\Control\TableControl;
use App\Control\TabMenuControl;

abstract class BasePresenter extends Presenter {

    protected static $SUCCESS = 'success';
    protected static $ERROR = 'error';

    public function beforeRender() {
        parent::beforeRender();

        /**
         * Menu items. Each item is associative array TEXT => [TARGET, [...SUBPAGE_PRESENTERS], ?[URL_PARAMETER]].
         */
        $this->template->menuItems = [
            'Domů' => ['Homepage:', []],
            'Vyučující' => ['Teacher:', ['Role']],
            'Pracoviště' => ['Department:', ['Faculty', 'Room']],
            'Předměty' => ['Subject:', ['CompletionType', 'TeachingForm', 'Category', 'CourseType']],
            'Obory' => ['StudyField:', []],
            'Plány' => ['StudyPlan:', ['SubjectInField', 'CourseTypeInField', 'Teaching']],
            'Rozvrhy' => ['Schedule:', []]
        ];

        if ($this->user->isInRole('admin')) {
            $this->template->menuItems['Uživatelé'] = ['User:', []];
        }

        if ($this->user->isLoggedIn()) {
            $this->template->menuItems['Můj účet'] = ['User:edit', [], $this->getUser()->id];
        } else {
            $this->template->menuItems['Přihlášení'] = ['Sign:in', []];
        }

        $this->template->isAdmin = $this->user->isInRole('admin');
        $this->template->isTeacher = $this->user->isInRole('teacher');
    }

    protected function createComponentTable() {
        return new TableControl();
    }

    protected function createComponentTabMenu() {
        return new TabMenuControl();
    }

    protected function showErrorMessage(DriverException $exception) {
        switch ($exception->getDriverCode()) {
            case 1:
                $message = 'Porušena unikátnost záznamů.';
                break;
            case 1400:
                $message = 'Některá ze zadaných hodnot nesmí být prázdná.';
                break;
            case 2292;
                $message = 'Nevyřešené závislosti (záznam nejde smazat dokud nebudou smazány všechny záznamy, které na něm závisí).';
                break;
            case 12899;
                $message = 'Zadaná hodnota je příliš dlouhá.';
                break;
            default:
                $code = 'ORA-' . $exception->getDriverCode();
                $message = "Něco se pokazilo (code: $code).";
                break;
        }
        $this->flashMessage($message, self::$ERROR);
    }

    protected function requireAdmin() {
        if (!$this->user->isInRole('admin')) {
            $this->flashMessage('Nedostatečná oprávnění.', self::$ERROR);
            $this->redirect('Homepage:');
        }
    }

}