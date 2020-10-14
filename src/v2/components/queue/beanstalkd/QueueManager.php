<?php

namespace concepture\yii2handbook\v2\components\queue\beanstalkd;

use concepture\yii2handbook\components\queue\events\BeforeSendEvent;
use concepture\yii2handbook\v2\components\queue\beanstalkd\worker\BaseWorker;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use udokmeci\yii2beanstalk\Beanstalk;
use Pheanstalk\PheanstalkInterface;
use concepture\yii2handbook\components\queue\BaseQueueManager;
use concepture\yii2handbook\services\DomainService;
use concepture\yii2handbook\components\queue\beanstalkd\BaseTubeEnum;
use udokmeci\yii2beanstalk\BeanstalkController;
use concepture\yii2handbook\components\queue\beanstalkd\QueueCollection;

/**
 * Сервис для работы с очередями через менеджер - beanstalkd
 *
 * Пример подключения в конфигурационном файле :
 * 'components' => [
 *      ...
 *      'queue'=>[
 *          'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueManager',
 *          'host' => '127.0.0.1', // default host
 *          'port' => 11300, //default port
 *          'connectTimeout' => 1,
 *          'sleep' => false, // or int for usleep after every job
 *          'enumClass' => 'common\enum\TubeEnum' // class of tube enum
 *      ],
 *      ...
 * ]
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class QueueManager extends BaseQueueManager
{
    /**
     * @var string
     */
    public $enumClass = '\common\components\queue\beanstalkd\QueueEnum';
    /**
     * @var string
     */
    public $host = "127.0.0.1";
    /**
     * @var int
     */
    public $port = 11300;
    /**
     * @var int
     */
    public $connectTimeout = 1;
    /**
     * @var bool
     */
    public $connected = false;
    /**
     * @var bool
     */
    public $sleep = false;
    /**
     * @var PheanstalkInterface
     */
    private $_pheanstalk;
    /**
     * @var QueueCollection
     */
    private $_collection;
    /**
     * Redis buffer key prefix
     * @var string
     */
    const REDIS_KEY_PREFIX = 'deduplication_buffer_';
    /**
     * Buffer lifetime in seconds
     * @var integer
     */
    const DEDUPLICATION_BUFFER_TTL = 30;
    /**
     * @var deduplicateQueue
     */
    public $deduplicateQueue = [];
    /**
     * @var redisComponent
     */
    public $redisComponent;
    /**
     * @var redis
     */
    private $redis;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if(! $this->_pheanstalk) {
            $config =                 [
                'class' => Beanstalk::class,
                'host' => $this->host,
                'port' => $this->port,
                'connectTimeout' => $this->connectTimeout,
                'connected' => $this->connected,
                'sleep' => $this->sleep,
            ];
            $this->_pheanstalk = Yii::createObject($config);
        }

        $reflection = new \ReflectionClass($this->enumClass);
        $parrentClass = $reflection->getParentClass()->name ?? null;
        if(null === $parrentClass || $parrentClass !== BaseTubeEnum::class) {
            throw new QueueManagerException("`enumClass` must be instance of \common\components\queue\beanstalkd\QueueEnum but instance of {$parrentClass}");
        }
        $this->redis = $this->getRedis();
        $self = $this;
        $queues = $this->deduplicateQueue;
        Event::on(BaseWorker::class, BaseWorker::EVENT_EXECUTE_JOB_DELETE, function($event) use ($self, $queues){
            \Yii::warning('Event triggered DELETE' . $event->queueName);
            if(in_array($event->queueName, $queues)) {
                $self->unregisterPayload($event->queueName, $event->payload);
            }
        });
        Event::on(BaseWorker::class, BaseWorker::EVENT_EXECUTE_JOB_BURY, function($event) use ($self, $queues){
            \Yii::warning('Event triggered BURY ' . $event->queueName . ' ' . $event->error);
            if(in_array($event->queueName, $queues)) {
                $self->unregisterPayload($event->queueName, $event->payload);
            }
        });
    }

    /**
     * @return PheanstalkInterface
     */
    public function getPheanstalk()
    {
        return $this->_pheanstalk;
    }

    /**
     * @return QueueCollection
     */
    public function getCollection()
    {
        if(! $this->_collection) {
            $this->_collection = QueueCollection::getInstance();
        }

        return $this->_collection;
    }

    /**
     * @return DomainService
     */
    protected function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * Добавление задачи в трубу
     *
     * @param string $tube
     * @param mixed $payload
     * @param int $priority
     * @param int $ttr
     * @param int $delay
     *
     * @throws QueueManagerException
     */
    public function putIn(string $tube, array $payload, $priority = 1024, $ttr = 0,  $delay = 0)
    {
        $enumClass = $this->enumClass;
        $allowedTubes = $enumClass::values();
        if(! in_array($tube, $allowedTubes)) {
            throw new QueueManagerException("Tube constant is not declarate in `{$enumClass}`");
        }
        # если явно передали идентификатор домена подменяем альяс
        if(isset($payload['domain_id'])) {
            $domain = $this->getDomainService()->findById((int) $payload['domain_id']);
            if($domain) {
                $payload['alias'] = $domain['alias'];
                unset($payload['domain_id']);
            }
        }

        $this->beforeSend($tube, $payload);

        // check duplicates here
        if(in_array($tube, $this->deduplicateQueue) && $this->existsPayload($tube, $payload)){
            return true;
        }

        $data = Json::encode($payload);
        $result = $this->getPheanstalk()->putInTube($tube, $data, $priority, $delay, $ttr);
        if(! $result) {
            throw new QueueManagerException('Failed to put the task into the tube.');
        }

        if(in_array($tube, $this->deduplicateQueue)){
            $this->registerPayload($tube, $payload);
        }
        $this->afterSend($tube, $payload);

        return $result;
    }

    /**
     * Освобождение коллекции задачь
     *
     * @return bool
     */
    public function flushCollection()
    {
        if(! $this->getCollection()->count()) {
            return false;
        }

        $collection = $this->getCollection()->get();
        foreach ($collection as $tube => $values) {
            foreach ($values as $params) {
                list($payload, $priority, $delay, $ttr) = array_values($params);
                $this->putIn($tube, $payload, $priority, $ttr, $delay);
            }
        }

        $this->getCollection()->clear();

        return true;
    }

    /**
     * @return mixed|object|null
     * @throws InvalidConfigException
     */
    public function getRedis()
    {
        if(! Yii::$app->has($this->redisComponent)) {
            throw new InvalidConfigException('`redisComponent` must be set');
        }

        return \Yii::$app->{$this->redisComponent};
    }

    /**
     * Check payload already exists in deduplication buffer
     *
     * @param $tube
     * @param $payload
     * @return mixed
     */
    private function existsPayload($tube, $payload)
    {
        return (bool)$this->redis->executeCommand('EXISTS', [
            self::REDIS_KEY_PREFIX . $tube . $this->preparePayload($payload)
        ]);
    }

    /**
     * Add payload to deduplication buffer
     *
     * @param $tube
     * @param $payload
     * @return mixed
     */
    private function registerPayload($tube, $payload)
    {
        return $this->redis->executeCommand('SETEX', [
            self::REDIS_KEY_PREFIX . $tube . $this->preparePayload($payload),
            self::DEDUPLICATION_BUFFER_TTL,
            1
        ]);
    }

    /**
     * Remove payload from deduplication buffer
     *
     * @param $tube
     * @param $payload
     * @return mixed
     */
    public function unregisterPayload($tube, $payload)
    {
        return $this->redis->executeCommand('DEL', [
            self::REDIS_KEY_PREFIX . $tube . $this->preparePayload($payload)
        ]);
    }

    private function preparePayload($payload)
    {
        // md5 or... nothing
        return md5(json_encode($payload));
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class QueueManagerException extends \Exception {}