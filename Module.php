<?php

namespace segic\userThin;

use dektrium\user\Module as DektriumModule;

class Module extends DektriumModule {

    /**
     * @var String
     * @desc Specify the controller name space
     */
    public $controllerNamespace = 'dektrium\user\controllers';

    /**
     * @var Array
     * @desc Configuration LDAP
     */
    public $ldapConfig = [
        'host' => 'ldap.usach.cl',
        'pass' => 'usach2008',
        'dn' => 'cn=orcladmin, cn=Users, dc=usach,dc=cl',
        'users' => 'cn=Users, dc=usach,dc=cl',
        'filter' => '(uid=%s)',
    ];

    /**
     * @var boolean
     * @desc Specify if a user auto created in system
     */
    public $allowAutoCreation = false;

    /**
     * @var String
     * @desc Form type (ldap or classic or mixed)
     */
    public $formType = 'classic';

    /**
     * @var boolean
     * @desc force to use internal views of this package
     */
    public $forceView = true;

}
