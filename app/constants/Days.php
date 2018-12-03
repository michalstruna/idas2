<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 01/12/2018
 * Time: 13:31
 */

namespace App\Constants;

class Days {

    use Enum;

    const MONDAY = ['index' => 0, 'text' => 'Pondělí'];
    const TUESDAY = ['index' => 1, 'text' => 'Úterý'];
    const WEDNESDAY = ['index' => 2, 'text' => 'Středa'];
    const THURSDAY = ['index' => 3, 'text' => 'Čtvrtek'];
    const FRIDAY = ['index' => 4, 'text' => 'Pátek'];
    const SATURDAY = ['index' => 5, 'text' => 'Sobota'];
    const SUNDAY = ['index' => 6, 'text' => 'Neděle'];

}