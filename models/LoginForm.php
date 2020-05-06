<?php

namespace segic\userThin\models;

use segic\userThin\helpers\Password;
use dektrium\user\models\LoginForm as BaseLoginForm;
use dektrium\user\models\User;
use Yii;

class LoginForm extends BaseLoginForm {

    /** @var string Type's form (LDAP or Classic) */
    public $type;
    public $userID;

    /** @static constant type form */
    const TYPE_LDAP = 1;
    const TYPE_CLASSIC = 2;
    const AFTER_LDAP_LOGIN_CREATE = 'afterLdapLoginCreate';

    public function init() {
        if (class_exists('\\app\\components\\UserEvents')) {
            $this->on(self::AFTER_LDAP_LOGIN_CREATE, ['app\components\UserEvents', 'handleAfterCreate']);
        }
    }

    /** @inheritdoc */
    public function rules() {

        $rules = parent::rules();
        $rules['type'] = ['type', 'integer'];

        $rules['passwordValidate'] = [
            'password',
            function ($attribute) {

                if ($this->type == LoginForm::TYPE_LDAP) {

                    if ($this->user === null && !Yii::$app->getModule('userThin')->allowAutoCreation) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    } else {

                        $ldapConfig = Yii::$app->getModule('userThin')->ldapConfig;
                        $dn = $ldapConfig['dn'];
                        $host = $ldapConfig['host'];
                        $pass = $ldapConfig['pass'];
                        $users = $ldapConfig['users'];
                        $filter = $ldapConfig['filter'];

                        $connection = ldap_connect($host);
                        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
                        Yii::trace("Try LDAP Connect : $host", __METHOD__);

                        if ($connection) {
                            Yii::trace("Connection LDAP OK.", __METHOD__);
                            $bind = ldap_bind($connection, $dn, $pass);

                            if (!$bind) {
                                Yii::trace("Error password LDAP.", __METHOD__);
                                $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                            } else {

                                $result = ldap_search($connection, $users, sprintf($filter, $this->login));

                                if (!$result) {
                                    Yii::trace("Error user unknown LDAP.");
                                    $this->addError($attribute, Yii::t('user', 'Invalid login'));
                                } else {
                                    if (ldap_count_entries($connection, $result) != 1) {
                                        Yii::trace("Posible multiple users with same ident LDAP.");
                                        $this->addError($attribute, Yii::t('user', 'Invalid login'));
                                    } else {
                                        $entry = ldap_first_entry($connection, $result);

                                        if ($entry) {
                                            $pass = ldap_get_values($connection, $entry, 'userpassword');
                                            $rut = ldap_get_values($connection, $entry, 'employeeNumber');

                                            if ($pass[0] == Password::validateLDAP($this->password)) {
                                                Yii::trace("Check password user LDAP OK.", __METHOD__);
                                                if (Yii::$app->getModule('userThin')->allowAutoCreation && $this->user === null) {

                                                    $user = new User();
                                                    $user->username = $this->login;
                                                    $user->email = $this->login . '@usach.cl';
                                                    $user->password = $this->password;

                                                    if ($user->create()) {
                                                        $this->user = $user;
                                                        $this->userID = $user->id;
                                                        $this->trigger(self::AFTER_LDAP_LOGIN_CREATE);
                                                    } else {
                                                        Yii::trace("Error create new user with allowAutoCreation.", __METHOD__);
                                                        $this->addError($attribute, Yii::t('user', 'Invalid login or password')); // TODO : change message ?
                                                    }
                                                }

                                                $session = Yii::$app->session;
                                                $session->set('rut', $rut[0]);
                                            } else {
                                                Yii::trace("Password from LDAP incorrect.", __METHOD__);
                                                $this->addError($attribute, Yii::t('user', 'Current password is not valid'));
                                            }
                                        } else {
                                            $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($this->user === null || !Password::validate($this->password, $this->user->password_hash)) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    }
                }
            }
        ];

        return $rules;
    }

    /** @inheritdoc */
    public function beforeValidate() {
        if (parent::beforeValidate()) {

            $this->login = ((strpos($this->login, '@') !== false) ? explode('@', $this->login)[0] : $this->login);
            $this->user = $this->finder->findUserByUsernameOrEmail(trim($this->login));

            return true;
        } else {
            return false;
        }
    }

}
