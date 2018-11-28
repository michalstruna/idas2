<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:10
 */

namespace App\Model;


class SubjectInPlanModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_PREDM_PLAN');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_PREDM_PLAN WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE SEM_PREDM_PLAN SET pocet_studentu=?, rocnik=?, kategorie_id=?, studijni_plan_id=?, predmet_id=?, semestr_id=? WHERE ID=?',
            $changes['studentCount'],
            $changes['year'],
            $changes['category'],
            $changes['studyPlan'],
            $changes['subject'],
            $changes['semester'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_PREDM_PLAN WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_PREDM_PLAN (id, pocet_studentu, rocnik, kategorie_id, studijni_plan_id, predmet_id, semestr_id) VALUES (SEM_PREDM_PLAN_SEQ.NEXTVAL, ?, ?, ?, ?, ?, ?)',
            $item['studentCount'],
            $item['year'],
            $item['category'],
            $item['studyPlan'],
            $item['subject'],
            $item['semester']
        );
    }
}