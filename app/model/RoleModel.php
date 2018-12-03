<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-26
 * Time: 21:49
 */

namespace App\Model;


class RoleModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_ROLE ORDER BY NAZEV');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_ROLE WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query('UPDATE SEM_ROLE SET NAZEV=? WHERE ID=?', $changes['name'],  $id);
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_ROLE WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query('INSERT INTO SEM_ROLE (id, nazev) VALUES (SEM_ROLE_SEQ.NEXTVAL, ?)', $item['name']);
    }

}