<?php

namespace concepture\yii2handbook\components\queue\beanstalkd;

use Yii;
use yii\helpers\Json;
use udokmeci\yii2beanstalk\Beanstalk;
use Pheanstalk\PheanstalkInterface;
use concepture\yii2handbook\components\queue\BaseQueueManager;

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

        $data = Json::encode($payload);
        $this->beforeSend($tube, $payload);
        $result = $this->getPheanstalk()->putInTube($tube, $data, $priority, $delay, $ttr);
        if(! $result) {
            throw new QueueManagerException('Failed to put the task into the tube.');
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
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class QueueManagerException extends \Exception {}