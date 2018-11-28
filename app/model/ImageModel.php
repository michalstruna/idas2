<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-28
 * Time: 13:10
 */

namespace App\Model;


use PDO;

class ImageModel extends BaseModel {

    function getById($id) {
        return $this->database->fetch('SELECT * FROM SEM_OBRAZEK WHERE ID = ?', $id);
    }

    function insert($image, $suffix) {

        $statement = $this->database->getPdo()->prepare('INSERT INTO SEM_OBRAZEK (id, obrazek, pripona, vytvoreno) VALUES (SEM_OBRAZEK_SEQ.NEXTVAL, :obrazek, :pripona, SYSDATE)');

        $test = base64_encode(addslashes($image));
        $statement->bindParam(':obrazek', $test, PDO::PARAM_LOB);
        $statement->bindParam(':pripona', $suffix);

        return $statement->execute();

        return $this->database->query(
            'INSERT INTO SEM_OBRAZEK (id, obrazek, pripona, vytvoreno) VALUES (SEM_OBRAZEK_SEQ.NEXTVAL, ?, ?, SYSDATE)',
            $image,
            $suffix
        );
    }
}