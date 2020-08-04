<?php

namespace concepture\yii2handbook\v2\components\queue\events;

use yii\base\Event;

/**
 * Событие после выполенения задачи
 */
class ExecuteJob extends Event
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