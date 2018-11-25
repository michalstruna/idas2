<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:36
 */

namespace App\Model;


class CompletionTypeModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array
    {
        return $this->database->fetchAll('SELECT * FROM SEM_ZPUS_ZAK');
    }

    public function getById(string $id)
    {
        return $this->database->fetch('SELECT * FROM SEM_ZPUS_ZAK WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void
    {
        $this->database->query('UPDATE SEM_ZPUS_ZAK SET NAZEV=? WHERE ID=?', $changes['name'], $id);
    }

    public function deleteById(string $id): void
    {
        $this->database->query('DELETE FROM SEM_ZPUS_ZAK WHERE ID=?', $id);
    }

    public function insert(array $item): void
    {
        $this->database->query('INSERT INTO SEM_ZPUS_ZAK (id, nazev) VALUES (SEM_ZPUSOB_ZAKONCENI_SEQ.NEXTVAL, ?)', $item['name']);
    }
}