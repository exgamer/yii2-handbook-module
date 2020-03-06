<?php

namespace concepture\yii2handbook\components\queue\beanstalkd;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;
use concepture\yii2handbook\components\queue\beanstalkd\QueueManager;

/**
 * Консольная команда для работы с очередями - beanstalkd
 *
 * php yii queue/put test a::a,c::c,e::e --alias=euro
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class QueueCommand extends Controller
{
    /**
     * @var string
     */
    public $queueComponent = 'queue';

    /**
     * @return QueueManager
     */
    protected function getQueueManager()
    {
        return Yii::$app->{$this->queueComponent};
    }

    /**
     * Закинуть задачу в очередь
     *
     * @param string $tube
     * @param string $payload передаетcя a::b,c::d,e::f
     * @return int|void
     * @throws QueueManagerException
     */
    public function actionPut(string $tube, string $payload)
    {
        $result = $this->parsePayload($payload);
        try {
            $this->getQueueManager()->putIn($tube, $result);

            $this->stdout("Payload : ", Console::FG_GREEN);
            $this->stdout(Json::encode($result) , Console::FG_YELLOW);
            $this->stdout( " successfully put in tube." . "\n", Console::FG_GREEN);
        } catch (\Exception $e) {
            $this->stderr($e->getMessage() . "\n", Console::FG_RED);
        }
    }

    /**
     * Парсит строку полезной нагрузки в массив
     *
     * @param string $payload
     *
     * @return array
     */
    private function parsePayload(string $payload)
    {
        $result = [];
        $arguments = explode(',', $payload);
        if(! $arguments) {
            return $result;
        }

        foreach ($arguments as $argument) {
            list($key, $value) = explode('::', $argument);
            $result[$key] = $value;
        }

        return $result;
    }
}