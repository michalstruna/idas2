<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 2018-12-03
 * Time: 09:40
 */

namespace App\Model;


class OciLoginProvider {

    public $ociLogin;

    /**
     * OciLoginProvider constructor.
     * @param $ociLogin
     */
    public function __construct(array $ociLogin) {
        $this->ociLogin = $ociLogin;
    }


}