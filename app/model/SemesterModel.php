<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-27
 * Time: 23:37
 */

namespace App\Model;


class SemesterModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_SEMESTR ORDER BY NAZEV');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_SEMESTR WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query('UPDATE SEM_SEMESTR SET NAZEV=? WHERE ID=?', $changes['name'], $changes['capacity'], $id);
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_SEMESTR WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query('INSERT INTO SEM_SEMESTR (id, nazev) VALUES (SEM_SEMESTR_SEQ.NEXTVAL, ?)', $item['name']);
    }
}