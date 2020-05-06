<?php

use yii\widgets\ActiveForm;
use segic\userThin\models\LoginForm;
use yii\helpers\Html;
use dektrium\user\widgets\Connect;

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;

$layout = '@segic/theme/layouts/main-login.php';
if (is_file(Yii::getAlias($layout, false))) {
    $this->context->layout = $layout;
}
?>

<div class="login-box">
    <div class="login-logo">
        <a href="../../index2.html"><b><?= Html::encode($this->title) ?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('user', 'Connect') ?></p>
        <?php
        $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnBlur' => false,
                    'validateOnType' => false,
                    'validateOnChange' => false,
                ])
        ?>

        <?php if (Yii::$app->getModule('userThin')->formType == 'mixed'): ?>
            <?= $form->field($model, 'type')->dropDownList([LoginForm::TYPE_LDAP => 'Usuario interno', LoginForm::TYPE_CLASSIC => 'Usuario externo'])->label(false) ?>
            <?php
            //$changeSrc = "\$('#UserLogin_typeForm').change(function(){if(this.value=='".UserLogin::TYPE_LDAP."'){\$('#helpLDAP').show();}else{\$('#helpLDAP').hide();}});";
            //Yii::app()->clientScript->registerScript('script',$changeSrc, CClientScript::POS_READY);
            ?>
        <?php elseif (Yii::$app->getModule('userThin')->formType == 'ldap'): ?>
            <?= $form->field($model, 'type')->hiddenInput(['value' => LoginForm::TYPE_LDAP])->label(false); ?>
            <?php
            //Yii::app()->clientScript->registerScript('script','$("#helpLDAP").show();', CClientScript::POS_READY);
            ?>
        <?php else: ?>
            <?= $form->field($model, 'type')->hiddenInput(['value' => LoginForm::TYPE_CLASSIC])->label(false); ?>
            <?php
            //Yii::app()->clientScript->registerScript('script', '$("#helpLDAP").hide();', CClientScript::POS_READY);
            ?>
        <?php endif; ?>

        <div class="form-group has-feedback">
            <?= $form->field($model, 'login')->textInput()->input('text', ["placeholder" => Yii::t('user', 'Login')])->label(false) ?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password')->textInput()->input('password', ["placeholder" => Yii::t('user', 'Password')])->label(false) ?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8">
                <div class="checkbox icheck">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
            </div>
            <!-- /.col -->
        </div>
        <?php ActiveForm::end(); ?>

        <!-- /.social-auth-links -->
        <!--
        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                Google+</a>
        </div>
        -->

        <?php if ($module->enableConfirmation): ?>
            <p class="text-center">
                <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
            </p>
        <?php endif ?>
        <?php if ($module->enableRegistration): ?>
            <p class="text-center">
                <?= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register']) ?>
            </p>
        <?php endif ?>

        <?=
        Connect::widget([
            'baseAuthUrl' => ['/user/security/auth'],
        ])
        ?>
    </div>
    <!-- /.login-box-body -->
</div>