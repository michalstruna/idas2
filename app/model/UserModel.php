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

final class UserModel extends BaseModel implements IAuthenticator, IUserModel {

    public function authenticate(array $credentials): IIdentity {
        // TODO: Get user from DB.
        if ($credentials[0] === 'admin@admin.cz' && $credentials[1] === 'secret') {
            // $user = $this->database->fetch('SELECT * FROM users WHERE email = ?', $credentials[0]);
            return new Identity('10de7e0a-5e2e-4282-bd3a-39b9be9c50b6');
        }

        throw new AuthenticationException('Invalid email or password.', self::IDENTITY_NOT_FOUND);
    }

}