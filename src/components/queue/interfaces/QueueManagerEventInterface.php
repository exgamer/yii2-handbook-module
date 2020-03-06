<?php

namespace concepture\yii2handbook\components\queue\interfaces;

/**
 * Интерфейс событий менеджера очередей
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
interface QueueManagerEventInterface
{
    const EVENT_BEFORE_SEND = 'beforeSend';
    const EVENT_AFTER_SEND = 'afterSend';

    /**
     * Событие перед отправкой задачи в очередь
     *
     * @param string $queueName
     * @param string $payload
     */
    function beforeSend($queueName, $payload);

    /**
     * Событие после отправкой задачи в очередь
     *
     * @param string $queueName
     * @param string $payload
     */
     function afterSend($queueName, $payload);
}