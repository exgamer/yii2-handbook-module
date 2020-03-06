<?php

namespace concepture\yii2handbook\components\queue\beanstalkd\worker;

/**
 * Интерфейс воркера
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
interface WorkerInterface
{
    /**
     * Название трубы для обработки
     *
     * @return string
     */
    public static function getTubeName();

    /**
     * Обработка данных
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function execute($payload);
}