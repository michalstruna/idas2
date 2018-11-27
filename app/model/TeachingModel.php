<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 22:11
 */

namespace App\Model;


class TeachingModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_UCI');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_UCI WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE SEM_UCI SET ucitel_id=?, role_id=?, predm_obor_id=? WHERE ID=?',
            $changes['teacher'],
            $changes['role'],
            $changes['subjectInField'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_UCI WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_UCI (id, ucitel_id, role_id, predm_obor_id) VALUES (SEM_UCI_SEQ.NEXTVAL, ?, ?, ?)',
            $item['teacher'],
            $item['role'],
            $item['subjectInField']
        );
    }
}