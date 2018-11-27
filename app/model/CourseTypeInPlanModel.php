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
            'UPDATE SEM_ZPUS_PREDM SET pocet_hodin=?, kapacita=?, zpusob_vyuky_id=?, predm_plan_id=? WHERE ID=?',
            $changes['hours'],
            $changes['capacity'],
            $changes['courseType'],
            $changes['subjectInPlan'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_ZPUS_PREDM WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_ZPUS_PREDM (id, pocet_hodin, kapacita, zpusob_vyuky_id, predm_plan_id) VALUES (SEM_ZPUS_PREDM_SEQ.NEXTVAL, ?, ?, ?, ?)',
            $item['hours'],
            $item['capacity'],
            $item['courseType'],
            $item['subjectInPlan']
        );
    }
}