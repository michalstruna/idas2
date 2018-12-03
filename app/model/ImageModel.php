<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-11-28
 * Time: 13:10
 */

namespace App\Model;

use Nette\Database\Connection;

class ImageModel extends BaseModel {

    public $ociLogin;

    /**
     * ImageModel constructor.
     * @param Connection $database
     * @param OciLoginProvider $ociLoginProvider
     */
    public function __construct(Connection $database, OciLoginProvider $ociLoginProvider) {
        parent::__construct($database);
        $this->ociLogin = $ociLoginProvider->ociLogin;
    }

    function getById($id) {
        return $this->database->fetch('SELECT * FROM SEM_OBRAZEK WHERE ID = ?', $id);
    }

    function insert($image, $type): string {
        $temp = explode('/', $type);
        $suffix = $temp[count($temp) - 1];
        $sql = "INSERT INTO SEM_OBRAZEK (id, obrazek, pripona, typ, vytvoreno) VALUES (SEM_OBRAZEK_SEQ.NEXTVAL, empty_blob(), :suffix, :type, SYSDATE) RETURNING id, obrazek INTO :id, :obrazek";

        $id = null;

        $connection = oci_connect($this->ociLogin['user'], $this->ociLogin['password'], $this->ociLogin['connection']);
        $result = oci_parse($connection, $sql);
        oci_bind_by_name($result, ':suffix', $suffix);
        oci_bind_by_name($result, ':type', $type);
        oci_bind_by_name($result, ':id', $id);
        // Image handling.
        $blob = oci_new_descriptor($connection, OCI_D_LOB);
        oci_bind_by_name($result, ":obrazek", $blob, -1, OCI_B_BLOB);
        oci_execute($result, OCI_DEFAULT) or die ("Unable to execute query");

        if (!$blob->save($image)) {
            oci_rollback($connection);
        } else {
            oci_commit($connection);
        }

        oci_free_statement($result);
        $blob->free();

        return $id;
    }
}