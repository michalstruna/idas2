<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:37
 */

namespace App\Model;


class CourseTypeModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_ZPUS_VYUKY ORDER BY NAZEV');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_ZPUS_VYUKY WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query('UPDATE SEM_ZPUS_VYUKY SET NAZEV=? WHERE ID=?', $changes['name'], $id);
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_ZPUS_VYUKY WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query('INSERT INTO SEM_ZPUS_VYUKY (id, nazev) VALUES (SEM_ZPUSOB_VYUKY_SEQ.NEXTVAL, ?)', $item['name']);
    }

}