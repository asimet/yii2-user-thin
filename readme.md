yii2-user-thin 
==========

features:
---------

* Overwrite dektrium package
* Offer control of corporate LDAP
* Check type of access : ldap, classic, mixed
* Auto create user if LDAP login is correct + assign role(s)/rule(s)


Install Composer
----------------

    {
        "repositories": [
            [...]
            {
                "type": "vcs",
                "url": ""
            }
        ],
        "require": {
            [...]
            "segic/yii2-user-thin": "dev-master",
        }
    }


Configuración (web.php)
------------------------

> Change config Dektrium configuration for overwrite models and module class : 

    'user' => [
        'class' => 'dektrium\user\Module',
        [...]
        'modelMap' => [
            'LoginForm' => 'segic\userThin\models\LoginForm',
        ],
    ]

> Add module definition :

    'modules' => [
        [...]
        'userThin' => [
            'class' => 'segic\userThin\Module',
            'formType' => 'mixed',
            'allowAutoCreation' => false, // Optional
            'forceView' => true, // Optional  (si no quiere usar la vista login del package definir false)
            'ldapConfig' => [
            ]
        ],
        [...]
    ]


Ejemplo Class despues "AutoCreation"
------------------------------------

    namespace app\components;

    use dektrium\user\models\User;
    use Yii;

    /**
    * @desc Administración de los usuarios que son creados automaticamente (funciona unicamente si allowAutoCreation = true)
    *
    */
    class UserEvents {

        public static function handleAfterCreate($event) {

            $id = $event->sender->userID;
            $user = User::findOne($id);

            $auth = Yii::$app->authManager;
            $role = $auth->getRole('role');
            $auth->assign($role, $user->id);

        }
    }
