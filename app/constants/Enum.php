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

    /**
     * Convert all constants of class to associative array.
     * @return array
     * @throws \ReflectionException
     */
    public static function toArray(): array {
        $reflection = new ReflectionClass(__CLASS__);
        return $reflection->getConstants();
    }

    /**
     * Find enum item by its nested property.
     * @param string $keyName Name of nested property.
     * @param $value Value of nested property.
     * @return array Enum item.
     * @throws \ReflectionException
     */
    public static function findByNestedKey(string $keyName, $value): array {
        foreach (self::toArray() as $item) {
            if (isset($item[$keyName]) && $item[$keyName] === $value) {
                return $item;
            }
        }

        return null;
    }

}