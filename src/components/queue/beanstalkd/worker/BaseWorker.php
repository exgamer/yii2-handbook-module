<?php

namespace concepture\yii2handbook\components\queue\beanstalkd\worker;

use Yii;
use yii\helpers\Console;
use yii\helpers\Json;
use yii\caching\CacheInterface;
use concepture\yii2handbook\components\queue\beanstalkd\QueueManager;
use udokmeci\yii2beanstalk\BeanstalkController;
use Pheanstalk\Job;

/**
 * Базовый воркер
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class BaseWorker extends BeanstalkController implements WorkerInterface
{
    /**
     * @var максимальное кол-во заданий для выполнения
     */
    public $limit = 0;
    /**
     * @var bool выполнять задачу в отдельном потоке через \Symfony\Component\Process\Process
     */
    public $stream = true;
    /**
     * @var string
     */
    public $queueComponent = 'queue';
    /**
     * @var string
     */
    public $storageComponent = 'workerStorage';
    /**
     * @var WorkerStreamService
     */
    private $_streamService;
    /**
     * @var int
     */
    private $_executeCount = 0;

    /**
     * @inheritDoc
     */
    public function options($actionID)
    {
        return ['limit'];
    }

    /**
     * @inheritDoc
     */
    public function optionAliases()
    {
        return ['l' => 'limit'];
    }

    /**
     * @return QueueManager
     */
    protected function getQueueManager()
    {
        return Yii::$app->{$this->queueComponent};
    }

    /**
     * @return WorkerStreamService
     */
    protected function getStreamService()
    {
        return $this->_streamService;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->beanstalk = $this->getQueueManager()->getPheanstalk();
        $storageComponent = Yii::$app->{$this->storageComponent};
        if(! $storageComponent instanceof CacheInterface) {
            throw new WorkerException('`storageComponent` must be instance of \yii\caching\CacheInterface.');
        }

        if($this->stream) {
            $this->_streamService = new WorkerStreamService($storageComponent);
        }

        $this->registerEvents();

        return parent::init();
    }

    /**
     * Возвращает название консольной команды
     *
     * @return string
     */
    public static function getCommandName()
    {
        $tube = static::getTubeName();

        return "worker:{$tube}";
    }

    /**
     * @inheritDoc
     */
    public function getTubes()
    {
        return [
            static::getTubeName()
        ];
    }

    /**
     * @inheritDoc
     */
    public function listenTubes()
    {
        return [
            static::getTubeName()
        ];
    }

    /**
     * Фикция для передачи обработки на actionProcess
     *
     * @inheritDoc
     */
    public function hasMethod($name, $checkBehaviors = true)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTubeAction($statsJob)
    {
        return $this->stream ? 'actionStreamDirect' : 'actionExecute';
    }

    /**
     * Получение задачи из очереди и отправка на выполнение в отдельном потоке
     *
     * @param Job $job
     */
    protected function actionStreamDirect(Job $job)
    {
        $payload = (array) $job->getData();
        try {
            $result = $this->getStreamService()->run(static::getCommandName(), $payload);
            $this->stdout( "Task successfully sent to stream with result:" . "\n", Console::FG_GREEN);
            $this->stdout( "{$result}" . "\n", Console::FG_YELLOW);

            return self::DELETE;
        } catch (\Exception $e) {
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);

            return self::BURY;
        }
    }

    /**
     * Выполнение задачи из отдельного потока
     *
     * @param string $storageKey
     */
    public function actionStreamExecute($storageKey)
    {
        try {
            $data = $this->getStreamService()->getData($storageKey);
            if(! $data) {
                throw new WorkerException("value for key - {$storageKey} is empty.");
            }

            $this->getStreamService()->removeData($storageKey);
            $payload = Json::decode($data);
            $this->execute((array) $payload);
            $this->stdout("Payload : ", Console::FG_GREEN);
            $this->stdout(Json::encode($payload) , Console::FG_YELLOW);
            $this->stdout( " successfully completed." . "\n", Console::FG_GREEN);

        } catch (\Exception $e) {
            $this->stderr('<ERROR>');
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);
            $this->stderr($e->getTraceAsString() . "\n", Console::FG_RED);
            $this->stderr('</ERROR>');
        }
    }

    /**
     * Обычное выполнение задачи
     *
     * @param Job $job
     *
     * @return string
     */
    protected function actionExecute(Job $job)
    {
        $payload = $job->getData();
        $payload = json_decode(json_encode($payload), JSON_OBJECT_AS_ARRAY);
        try {
            $this->execute($payload);
            $this->stdout("Payload : ", Console::FG_GREEN);
            $this->stdout(Json::encode($payload) , Console::FG_YELLOW);
            $this->stdout( " successfully completed." . "\n", Console::FG_GREEN);

            return self::DELETE;
        } catch (\Exception $e) {
            $this->stderr('<ERROR>');
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);
            $this->stderr($e->getTraceAsString() . "\n", Console::FG_RED);
            $this->stderr('</ERROR>');

            return self::BURY;
        }
    }

    /**
     * Регистрация событий
     */
    private function registerEvents()
    {
        # накручивает счетчик задач
        $this->on(self::EVENT_BEFORE_JOB, function() {
            if($this->limit === 0) {
                return false;
            }

            $this->_executeCount ++;
            $this->stdout( "{$this->_executeCount}: ", Console::FG_YELLOW);

            return true;
        });
        # проверяет аксимальное кол-во выполненых задач и сверяет с ограничением
        $this->on(self::EVENT_AFTER_JOB, function() {
            if(
                $this->limit === 0
                || ( $this->_executeCount < $this->limit )
            ) {
                return true;
            }

            return $this->end();
        });
    }
}

/**
 * Исключение воркера
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class WorkerException extends \Exception
{

}