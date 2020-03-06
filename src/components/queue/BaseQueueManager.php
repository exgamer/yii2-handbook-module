<?php

namespace concepture\yii2handbook\components\queue;

use yii\base\Component;

use concepture\yii2handbook\components\queue\interfaces\QueueManagerEventInterface;
use concepture\yii2handbook\components\queue\events\BeforeSendEvent;
use concepture\yii2handbook\components\queue\events\AfterSendEvent;

/**
 * Базовый менеджер очередей
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class BaseQueueManager extends Component implements QueueManagerEventInterface
{
    /**
     * Событие перед отправкой задачи в очередь
     *
     * @param string $queueName
     * @param string $payload
     *
     * @return BeforeSendEvent
     */
    public function beforeSend($queueName, &$payload)
    {
        $event = new BeforeSendEvent();
        $event->queueName = $queueName;
        $event->payload = $payload;
        $this->trigger(self::EVENT_BEFORE_SEND, $event);
        $payload = $event->payload;

        return $event;
    }

    /**
     * Событие после отправкой задачи в очередь
     *
     * @param string $queueName
     * @param string $payload
     *
     * @return AfterSendEvent
     */
    public function afterSend($queueName, &$payload)
    {
        $event = new AfterSendEvent();
        $event->queueName = $queueName;
        $event->payload = $payload;
        $this->trigger(self::EVENT_AFTER_SEND, $event);
        $payload = $event->payload;

        return $event;
    }
}