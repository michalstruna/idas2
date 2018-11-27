<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 07/11/2018
 * Time: 17:12
 */

namespace App\Model;

use Nette\Database\Row;
use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;

final class UserModel extends BaseModel implements IAuthenticator, IDatabaseWrapper {

    public function authenticate(array $credentials): IIdentity {
        $user = $this->database->fetch('SELECT * FROM sem_uzivatel WHERE email = ?', $credentials[0]);

        if ($user !== null && Passwords::verify($credentials[1], $user['heslo'])) {
            return $this->handleUserIdentity($user);
        }

        throw new AuthenticationException('Neplatné jméno nebo heslo.', self::IDENTITY_NOT_FOUND);
    }

    /**
     * @param $id
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticateById($id): IIdentity {
        $user = $this->database->fetch('SELECT * FROM sem_uzivatel WHERE id = ?', $id);

        if ($user !== null) {
            return $this->handleUserIdentity($user);
        }

        throw new AuthenticationException('Neplatný uživatel.', self::IDENTITY_NOT_FOUND);
    }

    private function handleUserIdentity(Row $user): IIdentity {
        $roles = [];

        if ($user['admin']) {
            $roles[] = 'admin';
        }

        if ($user['ucitel_id'] !== null) {
            $roles[] = 'teacher';
        }

        $data = [
            'teacherId' => $user['ucitel_id']
        ];

        return new Identity($user['id'], $roles, $data);
    }

    public function getAll(): array {
        return $this->database->fetchAll('SELECT * FROM sem_uzivatel');
    }

    public function getById(string $id) {
        return $this->database->fetch('SELECT * FROM sem_uzivatel WHERE id = ?', $id);
    }

    public function updateById(string $id, array $changes): void {
        if (empty($changes['password'])) {
            $this->database->query(
                'UPDATE sem_uzivatel SET email = ?, ucitel_id = ? WHERE id = ?',
                $changes['email'],
                $changes['teacher'],
                $id
            );
        } else {
            $this->database->query(
                'UPDATE sem_uzivatel SET email = ?, ucitel_id = ?, heslo = ? WHERE id = ?',
                $changes['email'],
                $changes['teacher'],
                self::hashPassword($changes['password']),
                $id
            );
        }
    }

    public function deleteById(string $id): void {
        $this->database->query('DELETE FROM sem_uzivatel WHERE id = ?', $id);
    }

    public function insert(array $item): void {
        $this->database->query(
            ' INSERT INTO sem_uzivatel (id, email, heslo) VALUES (SEM_UZIVATEL_SEQ.NEXVAL, ?, ?',
            $item['email'],
            self::hashPassword($item['password'])
        );
    }

    /**
     * Hash password.
     * @param string $password Plain text password.
     * @return string Password hash.
     */
    private function hashPassword(string $password): string {
        return Passwords::hash($password, ['cost' => 10]);
    }


}