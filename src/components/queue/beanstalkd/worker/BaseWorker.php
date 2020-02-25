<?php

namespace concepture\yii2handbook\components\queue\beanstalkd\worker;

use Yii;
use yii\helpers\Console;
use yii\helpers\Json;
use concepture\yii2handbook\components\queue\beanstalkd\QueueManager;
use udokmeci\yii2beanstalk\BeanstalkController;
use Pheanstalk\Job;

/**
 * Базовый воркер
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class BaseWorker extends BeanstalkController
{
    /**
     * @var string
     */
    public $queueComponent = 'queue';

    /**
     * Название трубы для обработки
     *
     * @return string
     */
    abstract protected function getTubeName();

    /**
     * Обработка данных
     *
     * @param mixed $data
     */
    abstract protected function execute($payload);

    /**
     * @return QueueManager
     */
    protected function getQueueManager()
    {
        return Yii::$app->{$this->queueComponent};
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->beanstalk = $this->getQueueManager()->getPheanstalk();

        return parent::init();
    }

    /**
     * @inheritDoc
     */
    public function getTubes()
    {
        return [
            $this->getTubeName()
        ];
    }

    /**
     * @inheritDoc
     */
    public function listenTubes()
    {
        return [
            $this->getTubeName()
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
     * Любую трубу обрабатывает действие Process
     *
     * @inheritDoc
     */
    public function getTubeAction($statsJob)
    {
        return 'actionExecute';
    }

    /**
     * Действие по обработке задачи
     *
     * @param Job $job
     *
     * @return string
     */
    public function actionExecute(Job $job)
    {
        $payload = $job->getData();
        try {
            $this->execute($payload);
            $this->stdout("Payload : ", Console::FG_GREEN);
            $this->stdout(Json::encode($payload) , Console::FG_YELLOW);
            $this->stdout( " successfully completed." . "\n", Console::FG_GREEN);

            return self::DELETE;
        } catch (\Exception $e) {
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);

            return self::BURY;
        }
    }
}