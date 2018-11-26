<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:10
 */

namespace App\Model;


class SubjectInFieldModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_PREDM_OBOR');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_PREDM_OBOR WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE SEM_PREDM_OBOR SET pocet_studentu=?, rocnik=?, kategorie_id=?, obor_id=?, predmet_id=? WHERE ID=?',
            $changes['studentCount'],
            $changes['year'],
            $changes['category'],
            $changes['studyField'],
            $changes['subject'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_PREDM_OBOR WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_PREDM_OBOR (id, pocet_studentu, rocnik, kategorie_id, obor_id, predmet_id) VALUES (SEM_PREDM_OBOR_SEQ.NEXTVAL, ?, ?, ?, ?, ?)',
            $item['studentCount'],
            $item['year'],
            $item['category'],
            $item['studyField'],
            $item['subject']
        );
    }
}