<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 24/11/2018
 * Time: 16:50
 */

namespace App\Control;

use \Nette\Application\UI\Control;

/**
 * Class TableControl
 * @package App\Control
 * Convert array of items to table.
 * Presenter in which TableControls is rendered must have renderEdit(string $id) and actionDelete(string $id) methods.
 */
class TableControl extends Control {

    /**
     * Render control.
     * @param array $items Array of items. Each item must have 'id' property.
     * @param bool $editable There are buttons EDIT and DELETE in each row.
     * @param array ...$columns Array of columns. Each item is array [$key, $text, ?$map].
     */
    public function render(array $items, bool $editable, array ...$columns): void {
        $template = $this->template;
        $template->setFile(__DIR__ . '/templates/tableControl.latte');
        $template->items = $items;
        $template->editable = $editable;
        $template->columns = $columns;
        $template->handleEdit = $this->presenter->getName() . ':edit';
        $template->handleDelete = $this->presenter->getName() . ':delete';
        $template->handleAdd = $this->presenter->getName() . ':add';
        $template->render();
    }

}