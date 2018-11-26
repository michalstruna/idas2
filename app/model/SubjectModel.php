<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 25/11/2018
 * Time: 21:40
 */

namespace App\Model;


class SubjectModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM SEM_P_PREDMET');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM SEM_P_PREDMET WHERE ID = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE SEM_PREDMET SET zkratka=?, nazev=?, forma_vyuky_id=?, zpusob_zakonceni_id=?  WHERE ID=?',
            $changes['shortName'],
            $changes['name'],
            $changes['teachingForm'],
            $changes['completionType'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM SEM_PREDMET WHERE ID=?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO SEM_PREDMET (id, zkratka, nazev, forma_vyuky_id, zpusob_zakonceni_id) VALUES (SEM_UCITEL_SEQ.NEXTVAL, ?, ?, ?, ?)',
            $item['shortName'],
            $item['name'],
            $item['teachingForm'],
            $item['completionType']
        );
    }
}