<?php

namespace concepture\yii2handbook\components\queue\events;

use yii\base\Event;

/**
 * Событие после отправки задачи в очередь
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class AfterSendEvent extends Event
{
    /**
     * @var string
     */
    public $queueName;
    /**
     * @var array
     */
    public $payload = [];
}