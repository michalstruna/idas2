<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:34
 */

namespace App\Model;


class CategoryModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_KATEGORIE');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_KATEGORIE WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query('UPDATE SEM_KATEGORIE SET NAZEV=? WHERE ID=?', $changes['name'], $id);
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_KATEGORIE WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query('INSERT INTO SEM_KATEGORIE (id, nazev) VALUES (SEM_KATEGORIE_SEQ.NEXTVAL, ?)', $item['name']);
    }

}