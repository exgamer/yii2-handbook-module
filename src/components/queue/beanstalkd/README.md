- Подключение (пример common\config\main.php)
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
        ],
        // при работе со стримом обязательно подключить хранилище, реализующее интерфейс \yii\caching\CacheInterface
        'workerStorage' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'redis',
                'port' => 6379,
                'database' => 2,
            ]
        ]
      ...
    ],
  ...
```
- Закинуть задачу в очередь :
```php
    $payload = [
        'foo' => 'bar'
    ];
    
    Yii::$app->queue->putIn(TubeEnum::TEST, $payload);
```
- Воркер должен быть унаследован от класса `concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker`
 и реализовывать его функции. Один воркер на одну трубу - (пример `console\components\worker\TestWorker`)
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
- Подключение воркеров (пример console\config\main.php)
```php
  ...
    'controllerMap' => [
        ...
        TestWorker::getCommandName() => [
            'class' => TestWorker::class,
            'stream' => true // признак обработки в отдельном потоке или в общем, true по умолчанию
        ]
        ...
    ],
  ...
```
- Пример вызова `php yii worker:test --alias=alias -l=20` - выполняет 20 задач из очереди test и умирает, по умолчанию ограничения нет;
- Подключение консольной команды для работы с менеджером очередей (пример console\config\main.php)
```php
  ...
    'controllerMap' => [
        ...
        'queue' => [
            'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueCommand'
        ],
        ...
    ],
  ...
```
- Пример вызова `php yii queue/put test a::a,c::c,e::e` - закидываем тестовую задачу в трубу test;