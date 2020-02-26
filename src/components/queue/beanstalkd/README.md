- Подключение (например common\config\main.php)
```php
  ...
    'components' => [
      ...
        'queue' => [
           'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueManager',
           'host' => '127.0.0.1', 
           'port' => 11300,
           'connectTimeout' => 1,
           'sleep' => false, 
           'enumClass' => 'common\enum\TubeEnum'
        ]
      ...
    ],
  ...
```
-Воркер должен быть унаследован от класса `concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker `
 и реализовывать его функции. Один воркер на одну трубу - (например `console\components\worker\TestWorker`)
 ```php
    namespace console\components\worker;
    
    use common\enum\TubeEnum;
    
    /**
     * Тестовый воркер
     *
     * @author kamaelkz <kamaelkz@yandex.kz>
     */
    class TestWorker extends \concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker
    {
        /**
         * @inheritDoc
         */
        protected function getTubeName()
        {
            return TubeEnum::TEST;
        }
    
        /**
         * @inheritDoc
         */
        protected function execute($data)
        {
            //реализация логики обработки полезной нагрузки
        }
    }
 ```
- Подключение воркеров (например console\config\main.php)
```php
  ...
    'controllerMap' => [
        'worker:test' => [
            'class' => 'console\components\worker\TestWorker'
        ],
    ],
  ...
```
- Подключение консольной команды для работы с менеджером очередей (например console\config\main.php)
```php
  ...
    'controllerMap' => [
        'queue' => [
            'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueCommand'
        ]
    ],
  ...
```
- Пример вызова `php yii queue/put test a::a,c::c,e::e`