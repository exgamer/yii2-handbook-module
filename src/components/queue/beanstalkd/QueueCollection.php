<?php

namespace concepture\yii2handbook\components\queue\beanstalkd;

/**
 * Коллекция задач для очереди
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
final class QueueCollection
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array коллекция задач в очередь
     */
    private static $collection = [];

    private function __construct(){}

    private function __clone() {}

    /**
     * @return QueueCollection
     */
    public static function getInstance() : self
    {
        if(! self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Добавление в коллекцию
     *
     * @param string $tube
     * @param array $payload
     * @param int $priority
     * @param int $delay
     * @param int $ttr
     */
    public function push(string $tube, array $payload, $priority = 1024, $delay = 0, $ttr = 0)
    {
        self::$collection[$tube][] = [
            'payload' => $payload,
            'priority' => $priority,
            'delay' => $delay,
            'ttr' => $ttr
        ];
    }

    /**
     * Добавление в коллекцию c установкой ключа
     *
     * @param string $key
     * @param string $tube
     * @param array $payload
     * @param int $priority
     * @param int $delay
     * @param int $ttr
     */
    public function pushByKey(string $key, string $tube, array $payload, $priority = 1024, $delay = 0, $ttr = 0)
    {
        self::$collection[$tube][$key] = [
            'payload' => $payload,
            'priority' => $priority,
            'delay' => $delay,
            'ttr' => $ttr
        ];
    }

    /**
     * Возвращает коллекцию задач для очереди
     *
     * @return array
     */
    public function get() : array
    {
        return self::$collection;
    }

    /**
     * Признак содержания данных в коллекции
     *
     * @return bool
     */
    public function count()
    {
        return count($this->get());
    }

    /**
     * Очистка коллекции
     */
    public function clear()
    {
        self::$collection = [];
    }
}