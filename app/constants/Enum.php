<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 01/12/2018
 * Time: 13:32
 */

namespace App\Constants;

use ReflectionClass;

Trait Enum {

    static function toArray(): array {
        $reflection = new ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }

}