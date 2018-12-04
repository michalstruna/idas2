<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-03
 * Time: 19:40
 */

namespace App\Model;


class ObligationModel extends BaseModel {

    function getMyObligations($id): array {
        return $this->database->fetchAll(
            'SELECT * FROM sem_p_uvazky WHERE "ucitel" = ?',
            $id
        );
    }
}