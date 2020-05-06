<?php

namespace segic\userThin;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class registers module and user application component. It also creates some url rules which will be applied
 * when UrlManager.enablePrettyUrl is enabled.
 *
 */
class Bootstrap implements BootstrapInterface {

    /** @inheritdoc */
    public function bootstrap($app) {

        if (class_exists("\\dektrium\\user\\models\\LoginForm")) {

            if (Yii::$app->getModule('userThin')->forceView) {

                $views = $app->get('view');
                $pathMap = [];

                if (isset($views->theme) && isset($views->theme->pathMap) && is_array($views->theme->pathMap)) {
                    $pathMap = $views->theme->pathMap;
                }

                $pathMap['@dektrium/user/views'] = '@segic/userThin/views';

                $app->set('view', [
                    'class' => 'yii\web\View',
                    'theme' => [
                        'pathMap' => $pathMap
                    ]
                ]);
            }

            Yii::$container->set('dektrium\\user\\models\\LoginForm', 'segic\userThin\models\LoginForm');
            Yii::$container->set('dektrium\\user\\helpers\\Password', 'segic\userThin\helpers\Password');
        }
    }
}
