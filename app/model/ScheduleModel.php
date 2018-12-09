<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 2018-12-01
 * Time: 14.16
 */

namespace App\Model;

use Nette\Database\Connection;
use Nette\InvalidStateException;
use Nette\Security\User;

class ScheduleModel extends BaseModel implements IDatabaseWrapper, IScheduleModel {

    private $roomModel;
    private $courseTypeInPlanModel;
    private $user;

    public function __construct(Connection $database, RoomModel $roomModel, CourseTypeInPlanModel $courseTypeInPlanModel, User $user) {
        parent::__construct($database);
        $this->roomModel = $roomModel;
        $this->courseTypeInPlanModel = $courseTypeInPlanModel;
        $this->user = $user;
    }

    public function getAll(): array {
        return $this->database->fetchAll('SELECT sem_rozvrh.*, "ucitel_id" FROM sem_p_rozvrh');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT sem_rozvrh.*, TO_CHAR(presne_datum, \'YYYY-MM-DD\') AS "datum" FROM sem_rozvrh WHERE id = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        $this->validateAction(array_merge(['id' => $id], $changes));

        $this->database->query(
            'UPDATE sem_rozvrh SET den_v_tydnu = ?, zacatek = ?, mistnost_id = ?, zpusob_zakonceni_predmetu_id = ?, uci_id = ?, presne_datum = TO_DATE(?, \'YYYY-MM-DD\'), schvaleno = ? WHERE id = ?',
            $changes['day'],
            $changes['start'],
            $changes['room'],
            $changes['courseType'],
            $changes['teaching'],
            isset($changes['date']) ? $changes['date'] : '',
            $changes['approved'],
            $id
        );
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM sem_rozvrh WHERE id = ?', $id);
    }

    public function insert(array $item): void {
        $this->validateAction($item);

        $this->database->query(
            'INSERT INTO sem_rozvrh (id, den_v_tydnu, zacatek, mistnost_id, zpusob_zakonceni_predmetu_id, uci_id, presne_datum, schvaleno) VALUES (SEM_ROZVRH_SEQ.NEXTVAL, ?, ?, ?, ?, ?, TO_DATE(?, \'YYYY-MM-DD\'), ?)',
            $item['day'],
            $item['start'],
            $item['room'],
            $item['courseType'],
            $item['teaching'],
            isset($item['date']) ? $item['date'] : '',
            $item['approved']
        );
    }

    public function getByFilter(array $filter): array {
        $conditions = [];
        $parameters = [];
        $allowedFilters = ['"ucitel_id"', '"mistnost_id"', '"semestr_id"', '"plan_id"', '"rocnik"', 'schvaleno'];

        foreach ($filter as $key => $item) {
            if ($item !== null && in_array($key, $allowedFilters)) {
                array_push($conditions, $key . ' = ?');
                array_push($parameters, $item);
            }
        }

        $query = $this->database->getPdo()->prepare(
            'SELECT * FROM sem_p_rozvrh' . (empty($conditions) ? '' : (' WHERE ' . implode(' AND ', $conditions)))
        );

        $query->execute($parameters);

        return $query->fetchAll();
    }

    /**
     * Validate schedule action.
     * Check max. capacity of room.
     * Check if room is not full.
     * @param array $action
     * @throws \Nette\InvalidStateException
     */
    private function validateAction(array $action): void {
        $room = $this->roomModel->getById($action['room']);
        $courseType = $this->courseTypeInPlanModel->getById($action['courseType']);

        if ($room['kapacita'] < $courseType['kapacita']) {
            throw new InvalidStateException('Místnost není dostatečně velká.');
        }

        $isRoomEmpty = $this->database->fetchField(
            'SELECT sem_je_mistnost_volna(?, ?, ?, ?, ?) FROM dual',
            $action['room'],
            $action['day'],
            $action['start'],
            $action['start'] + $courseType['pocet_hodin'],
            isset($action['id']) ? $action['id'] : null
        );

        if (!intval($isRoomEmpty)) {
            throw new InvalidStateException('Místnost je obsazená.');
        }

        // Validate study plan.
        if (!$this->user->isInRole('admin')) {
            $isRoomEmpty = $this->database->fetchField(
                'SELECT sem_skupina_zaneprazdnena(?, ?, ?, ?, ?) FROM dual',
                $action['courseType'],
                $action['day'],
                $action['start'],
                $action['start'] + $courseType['pocet_hodin'],
                isset($action['id']) ? $action['id'] : null
            );

            if (!intval($isRoomEmpty)) {
                throw new InvalidStateException('Tento studijní plán má v tuto dobu jinou výuku.');
            }
        }
    }
}