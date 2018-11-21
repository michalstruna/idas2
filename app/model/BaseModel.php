<?php
/**
 * Created by PhpStorm.
 * User: Michal Struna
 * Date: 07/11/2018
 * Time: 17:10
 */

namespace App\Model;

use Nette\Database\Connection;

class BaseModel implements IBaseModel {

    protected $database;

    public function __construct(Connection $database) {
        $database->getPdo()->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
        $this->database = $database;
    }

}