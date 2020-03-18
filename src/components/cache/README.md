- Подключение компонента кеширования (пример common\config\main.php)
```php
    use use concepture\yii2handbook\components\cache\CacheService;
    ...
    'bootstrap' => [
        'common\components\cache\CacheBootstrap',
    ],
    ...
    'components' => [
      ...
        CacheService::COMPONENT_NAME => [
            'class' => CacheService::class,
            'cacheComponent' => 'redis',
            'queueComponent' => 'queue',
            'queueName' => TubeEnum::CACHE,
            'env' => APP_USERNAME . '_' . APP_LOCALE,
            'locale' => APP_LOCALE,
        ],
      ...
    ],
    ...
```
- Подключение воркера (пример console\config\main.php)
```php
  ...
    'controllerMap' => [
        ...
        'worker:cache' => [
            'class' => CacheWorker::class,
            'stream' => false,
        ],
        ...
    ],
  ...
```
- Пример автозагрузчика для сброса кеша
```php
class CacheBootstrap extends \concepture\yii2handbook\components\cache\CacheBootstrap
{
    /**
     * @inheritDoc
     */
    public function configure($app, $event)
    {
        $serviceClass = get_class($event->sender);
        switch ($serviceClass) {
            case FooService::class:
                $this->addTag(CacheTagEnum::FOO);

                break;
        }
    }
}
    
```