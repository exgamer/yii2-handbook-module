<?php

namespace concepture\yii2handbook\components\cache;

use concepture\yii2logic\services\events\modify\AfterModifyEvent;
use yii\base\Event;
use yii\base\Application;
use yii\base\BootstrapInterface;
use concepture\yii2logic\services\Service;

/**
 * Автозагрузка для сброса кеша
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class CacheBootstrap implements BootstrapInterface
{
    use CacheServiceTrait;

    /**
     * @var array
     */
    private $buffer = [
        'tag' => [],
        'key' => []
    ];

    /**
     * Логика сброса кеша
     *
     * @param AfterModifyEvent $event
     * @return mixed
     */
    abstract function configure($app, AfterModifyEvent $event);

    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        Event::on(Service::class, Service::EVENT_AFTER_MODIFY, function ($event) use ($app) {
            $this->configure($app, $event);
            $this->clearCache();
        });
    }

    /**
     * @param string $value
     */
    protected function addTag($value)
    {
        $this->buffer['tag'][] = $value;
    }

    /**
     * @param string $value
     */
    protected function addKey($value)
    {
        $this->buffer['key'][] = $value;
    }

    /**
     * Очистка кеша из буфера
     */
    private function clearCache()
    {
        if(sizeof($this->buffer['tag'])){
            foreach($this->buffer['tag'] as $tag){
                $this->cacheService()->removeByTagQueue($tag);
            }
        }

        if(sizeof($this->buffer['key'])){
            foreach($this->buffer['key'] as $key){
                $this->cacheService()->removeByKey($key);
            }
        }

        $this->buffer = [
            'tag' => [],
            'key' => [],
        ];
    }
}