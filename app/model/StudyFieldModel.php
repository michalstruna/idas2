<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:38
 */

namespace App\Model;


class StudyFieldModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_OBOR');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_OBOR WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query('UPDATE SEM_OBOR SET NAZEV=?, ODHAD_STUDENTU=? WHERE ID=?', $changes['name'], $changes['students'], $id);
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_OBOR WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query('INSERT INTO SEM_OBOR (id, nazev, ODHAD_STUDENTU) VALUES (SEM_OBOR_SEQ.NEXTVAL, ?, ?)', $item['name'], $item['students']);
    }
}