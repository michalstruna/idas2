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

final class UserModel extends BaseModel implements IAuthenticator, IUserModel {

    public function authenticate(array $credentials): IIdentity {
        $user = $this->database->fetch('SELECT * FROM sem_uzivatel WHERE email = ?', $credentials[0]);

        // TODO: Change to heslo.
        // Can we force lowercase names??? Posible in PDO - not sure if nette supports it.
        if ($user != null && Passwords::verify($credentials[1], $user['PASSWORD'])) {
            return new Identity($user['ID']);
        }
        
        throw new AuthenticationException('Invalid email or password.', self::IDENTITY_NOT_FOUND);
    }

}