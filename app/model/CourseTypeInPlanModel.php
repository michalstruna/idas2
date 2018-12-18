<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:11
 */

namespace App\Model;


class CourseTypeInPlanModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_ZPUS_PREDM');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_ZPUS_PREDM WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'BEGIN SEM_NASTAV_ZPUS_VYUKY(?, ?, ?, ?, ?); END;',
            $id,
            $changes['hours'],
            $changes['capacity'],
            $changes['courseType'],
            $changes['subjectInPlan']
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_ZPUS_PREDM WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'BEGIN SEM_NASTAV_ZPUS_VYUKY(NULL, ?, ?, ?, ?); END;',
            $item['hours'],
            $item['capacity'],
            $item['courseType'],
            $item['subjectInPlan']
        );
    }
}