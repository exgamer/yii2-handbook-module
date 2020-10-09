<?php

namespace concepture\yii2handbook\components\multilanguage;

use Yii;
use yii\base\Event;
use yii\web\Application;
use yii\base\BootstrapInterface;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageBootstrap implements BootstrapInterface
{
    use MultiLanguageServiceTrait;

    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        # todo: реализация позже
        Event::on(Application::class, Application::EVENT_BEFORE_REQUEST, function($event) use ($app) {

        });
    }
}