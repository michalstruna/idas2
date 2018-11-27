<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 25/11/2018
 * Time: 14:13
 */

namespace App\Control;

use \Nette\Application\UI\Control;

/**
 * Class TabMenuControl
 * @package App\Control
 */
class TabMenuControl extends Control {

    /**
     * Render control.
     * @param array $tabs Associative array of links. Each item is pair text => action. Action also can be array [action, parameter].
     */
    public function render(array $tabs): void {
        $template = $this->template;
        $template->setFile(__DIR__ . '/templates/tabMenuControl.latte');
        $template->tabs = $tabs;
        $template->render();
    }

}