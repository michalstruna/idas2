<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 24/11/2018
 * Time: 16:16
 */

namespace App\Model;

interface IDatabaseWrapper {

    /**
     * Select items from DB.
     * @return array Array of DB items.
     */
    public function getAll(): array; // TODO: Array of selected columns in parameter?

    /**
     * Select one item from DB.
     * @param string $id ID of item.
     * @return mixed Item from DB.
     */
    public function getById(string $id);

    /**
     * Update items in DB.
     * @param string $id ID of item.
     * @param array $changes Array of changes where keys are DB columns.
     */
    public function updateById(string $id, array $changes): void;

    /**
     * Delete items from DB.
     * @param string $id ID of item.
     */
    public function deleteById(string $id): void;

    /**
     * Insert item to DB.
     * @param array $item Array of properties of new database item, where keys are DB columns.
     */
    public function insert(array $item): void;

}