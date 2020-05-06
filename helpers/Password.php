<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace segic\userThin\helpers;

use dektrium\user\helpers\Password as BasePassword;

class Password extends BasePassword {

    /**
     * @return encrypting LDAP string.
     */
    public static function validateLDAP($password = "") {
        return '{SHA}' . base64_encode(pack('H*', sha1($password)));
    }

}
