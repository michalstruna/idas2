<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:37
 */

namespace App\Model;


class RoomModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array
    {
        return $this->database->fetchAll('SELECT * FROM SEM_MISTNOST');
    }

    public function getById(string $id)
    {
        return $this->database->fetch('SELECT * FROM SEM_MISTNOST WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void
    {
        $this->database->query('UPDATE SEM_MISTNOST SET NAZEV=?, KAPACITA=? WHERE ID=?', $changes['name'], $changes['capacity'], $id);
    }

    public function deleteById(string $id): void
    {
        $this->database->query('DELETE FROM SEM_MISTNOST WHERE ID=?', $id);
    }

    public function insert(array $item): void
    {
        $this->database->query('INSERT INTO SEM_MISTNOST (id, nazev, kapacita) VALUES (SEM_MISTNOST_SEQ.NEXTVAL, ?, ?)', $item['name'], $item['capacity']);
    }
}