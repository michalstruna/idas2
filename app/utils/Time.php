<?php
/**
 * Created by PhpStorm.
 * User: Michal
 * Date: 01/12/2018
 * Time: 22:07
 */

namespace App\Utils;

class Time {

    /**
     * Format time interval.
     * @param int $start Start [h].
     * @param int $length Length [h].
     * @return string Formatted interval like 14-16 h.
     */
    public static function formatInterval(int $start, int $length): string {
        return $start . '-' . ($start + $length) . ' h';
    }

}