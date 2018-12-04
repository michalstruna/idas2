<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 24/11/2018
 * Time: 16:19
 */

namespace App\Model;


class FacultyModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM sem_fakulta ORDER BY NAZEV');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM sem_fakulta WHERE id = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE sem_fakulta SET nazev = ?, zkratka = ? where id = ?',
            $changes['name'],
            $changes['shortName'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM sem_fakulta WHERE id = ?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO sem_fakulta (id, nazev, zkratka) VALUES (SEM_FAKULTA_SEQ.NEXTVAL, ?, ?)',
            $item['name'],
            $item['shortName']
        );
    }


}