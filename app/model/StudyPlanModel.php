<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-27
 * Time: 22:35
 */

namespace App\Model;


class StudyPlanModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_STUD_PLAN');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_STUD_PLAN WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE SEM_STUD_PLAN SET NAZEV=?, ODHAD_STUDENTU=?, obor_id=? WHERE ID=?',
            $changes['name'],
            $changes['students'],
            $changes['studyField'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_STUD_PLAN WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_STUD_PLAN (id, nazev, ODHAD_STUDENTU, obor_id) VALUES (SEM_STUDIJNI_PLAN_SEQ.NEXTVAL, ?, ?, ?)',
            $item['name'],
            $item['students'],
            $item['studyField']
        );
    }
}