<?php
namespace concepture\yii2handbook;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'concepture\yii2handbook\web\controllers';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'concepture\yii2handbook\console\controllers';
        }
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['yii2handbook'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'ru-RU',
            'basePath' => '@vendor/concepture/yii2handbook/messages',
        ];

    }
}