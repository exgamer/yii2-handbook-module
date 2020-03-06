<?php

namespace concepture\yii2handbook\components\queue\events;

use yii\base\Event;

/**
 * Событие до отправки задачи в очередь
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class BeforeSendEvent extends Event
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