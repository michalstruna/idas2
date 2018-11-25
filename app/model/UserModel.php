<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 07/11/2018
 * Time: 17:12
 */

namespace App\Model;

use Nette\Security\IAuthenticator;
use Nette\Security\IIdentity;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;

final class UserModel extends BaseModel implements IAuthenticator {

    public function authenticate(array $credentials): IIdentity {
        $user = $this->database->fetch('SELECT * FROM sem_uzivatel WHERE email = ?', $credentials[0]);

        if ($user !== null && Passwords::verify($credentials[1], $user['heslo'])) {
            $roles = [];
            if ($user['admin'] === 1) {
                $roles[] = 'admin';
            }
            if ($user['ucitel_id'] !== null) {
                $roles[] = 'teacher';
            }
            $data = [
                'teacher_id' => $user['ucitel_id']
            ];
            return new Identity($user['id'], $roles, $data);
        }

        throw new AuthenticationException('Neplatné jméno nebo heslo.', self::IDENTITY_NOT_FOUND);
    }

}