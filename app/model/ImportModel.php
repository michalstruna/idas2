<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-01
 * Time: 21:16
 */

namespace App\Model;


class ImportModel extends BaseModel {

    function importTeachers($data) {
        for ($i = 0; $i < count($data['name']); $i++) {
            $this->database->query(
                'BEGIN SEM_IMPORT.IMPORT_UCITELE(?, ?, ?, ?, ?, ?, ?, ?); END;',
                $data['name'][$i],
                $data['surname'][$i],
                $data['titlePrefix'][$i],
                $data['titlePostfix'][$i],
                $data['telephone'][$i],
                $data['mobile'][$i],
                $data['email'][$i],
                $data['department'][$i]
            );
        }
    }

    function importSubjects($data) {
        for ($i = 0; $i < count($data['name']); $i++) {
            $this->database->query(
                'BEGIN SEM_IMPORT.IMPORT_PREDMETU(?, ?, ?, ?); END;',
                $data['shortName'][$i],
                $data['name'][$i],
                $data['teachingForm'][$i],
                $data['completionType'][$i]
            );
        }
    }

    function importDepartments($data) {
        for ($i = 0; $i < count($data['name']); $i++) {
            $this->database->query(
                'BEGIN SEM_IMPORT.IMPORT_KATEDRY(?, ?, ?); END;',
                $data['shortName'][$i],
                $data['name'][$i],
                $data['faculty'][$i]
            );
        }
    }
}