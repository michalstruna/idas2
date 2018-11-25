<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 25/11/2018
 * Time: 11:36
 */

namespace App\Model;


class DepartmentModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM sem_p_katedra');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM sem_katedra WHERE id = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE sem_katedra SET nazev = ?, zkratka = ?, fakulta_id = ? where id = ?',
            $changes['name'],
            $changes['shortName'],
            $changes['faculty'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM sem_katedra WHERE id = ?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO sem_katedra (id, nazev, zkratka, fakulta_id) VALUES (SEM_KATEDRA_SEQ.NEXTVAL, ?, ?, ?)',
            $item['name'],
            $item['shortName'],
            $item['faculty']
        );
    }


}