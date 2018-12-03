<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 03/12/2018
 * Time: 13:17
 */

namespace App\Model;


interface IScheduleModel {

    /**
     * Get schedule actions by filter.
     * @param array $filter
     * @return array Array of schedule actions.
     */
    public function getByFilter(array $filter): array;

}