<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 2018-12-01
 * Time: 14.16
 */

namespace App\Model;


class ScheduleModel extends BaseModel implements IDatabaseWrapper {

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM sem_p_rozvrh');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT sem_rozvrh.*, TO_CHAR(presne_datum, \'YYYY-MM-DD\') AS "datum" FROM sem_rozvrh WHERE id = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->database->query(
            'UPDATE sem_rozvrh SET den_v_tydnu = ?, zacatek = ?, mistnost_id = ?, zpusob_zakonceni_predmetu_id = ?, uci_id = ?, presne_datum = TO_DATE(?, \'YYYY-MM-DD\'), schvaleno = ? WHERE id = ?',
            $changes['day'],
            $changes['start'],
            $changes['room'],
            $changes['courseType'],
            $changes['teaching'],
            isset($changes['date']) ? $changes['date'] : '',
            false,
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM sem_rozvrh WHERE id = ?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            'INSERT INTO sem_rozvrh (id, den_v_tydnu, zacatek, mistnost_id, zpusob_zakonceni_predmetu_id, uci_id, presne_datum, schvaleno) VALUES (SEM_ROZVRH_SEQ.NEXTVAL, ?, ?, ?, ?, ?, TO_DATE(?, \'YYYY-MM-DD\'), ?)',
            $item['day'],
            $item['start'],
            $item['room'],
            $item['courseType'],
            $item['teaching'],
            isset($item['date']) ? $item['date'] : '',
            false
        );
    }
}