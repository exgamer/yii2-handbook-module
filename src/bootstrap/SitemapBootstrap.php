<?php
namespace concepture\yii2user\bootstrap;

use yii\base\Event;
use yii\base\Application;
use yii\base\BootstrapInterface;
use concepture\yii2logic\services\Service;

/**
 * Автозагрузка для карты сайта
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SitemapBootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        Event::on(Service::class, Service::EVENT_AFTER_MODIFY, function ($event) {
            $this->extendBefore();
            # логика рефреша
            $this->extendAfter();
        });
    }
    public function extendBefore()
    {
    }
    public function extendAfter()
    {
    }
}