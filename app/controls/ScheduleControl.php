<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 2/12/2018
 * Time: 15:30
 */

namespace App\Control;

use \App\Constants\Days;
use \Nette\Application\UI\Control;

/**
 * Class ScheduleControl
 * @package App\Control
 * Display list of schedule actions in 2D area.
 */
class ScheduleControl extends Control {

    /**
     * Render control.
     * @param array $scheduleActions Array of items. Each item must have 'id', 'zacatek', 'pocet_hodin', 'zpusob_vyuky', 'ucitel' and 'mistnost' properties.
     * @throws \ReflectionException
     */
    public function render(array $scheduleActions): void {
        $template = $this->template;
        $template->setFile(__DIR__ . '/templates/scheduleControl.latte');
        $template->items = $this->get2DArea($scheduleActions);
        $template->handleEdit = $this->presenter->getName() . ':edit';
        $template->handleDelete = $this->presenter->getName() . ':delete';
        $template->handleAdd = $this->presenter->getName() . ':add';
        $template->editable = true;

        $template->getDayNameByIndex = function ($index): string {
            return Days::findByNestedKey('index', (int)$index)['text'];
        };

        $template->render();
    }

    /**
     * Return 2D area of all schedule actions with their coordinates.
     * @param array $scheduleActions
     * @return array Array with 'area', 'days', 'xMin' and 'xMax' keys.
     * Each 'area' item is array with keys 'xMin', 'xMax', 'y', 'data'.
     * Each 'days' item is array with keys 'yMin', 'yMax', index.
     * @throws \ReflectionException
     */
    private function get2DArea(array $scheduleActions): array {
        $area = [];
        $resources = [];
        $daysYCoordinates = [];
        $xMin = 23;
        $xMax = 0;
        $globalMaxY = empty($daysYCoordinates) ? 1 : end($daysYCoordinates);

        foreach (Days::toArray() as $day) {
            $yMin = empty($daysYCoordinates) ? 2 : end($daysYCoordinates)['yMax'];
            $yMax = empty($daysYCoordinates) ? 2 : end($daysYCoordinates)['yMax'];

            foreach ($scheduleActions as $scheduleAction) {
                if ((int)$scheduleAction['den_v_tydnu'] === $day['index']) {
                    $start = $scheduleAction['zacatek'];
                    $end = $scheduleAction['zacatek'] + $scheduleAction['pocet_hodin'];
                    $y = $this->getFreeVerticalCoordinate($resources, $start, $end, $globalMaxY + 1);
                    $yMax = max($y, $yMax);
                    $xMin = min($start, $xMin);
                    $xMax = max($end, $xMax);

                    array_push($area, [
                        'xMin' => $start + 2,
                        'xMax' => $end + 2,
                        'y' => $y,
                        'data' => $scheduleAction
                    ]);
                }
            }

            if ((int)end($area)['data']['den_v_tydnu'] === $day['index'] && $xMax) {
                $globalMaxY = $yMax;
                array_push($daysYCoordinates, [
                    'yMin' => $yMin,
                    'yMax' => $yMax + 1,
                    'index' => $day['index']
                ]);
            }
        }

        return ['area' => $area, 'daysY' => $daysYCoordinates, 'xMin' => $xMin, 'xMax' => $xMax - 1];
    }

    /**
     * Find free vertical coordinate.
     * @param array $resources Array of full coordinates.
     * @param int $start Start hour of schedule action.
     * @param int $end End hour of schedule action.
     * @param int $minY
     * @return int Lowest free vertical coordinate.
     */
    private function getFreeVerticalCoordinate(array &$resources, int $start, int $end, int $minY): int {
        $coordinate = 0;
        $result = null;
        $hours = range($start, $end - 1);

        while (!$result) {
            $isFree = true;

            foreach ($hours as $hour) {
                if (isset($resources[$coordinate][$hour]) || $coordinate < $minY) {
                    $isFree = false;
                    break;
                }
            }

            if ($isFree) {
                $result = $coordinate;

                foreach ($hours as $hour) {
                    $resources[$coordinate][$hour] = true;
                }
            }

            $coordinate++;
        }

        return $result;
    }

}