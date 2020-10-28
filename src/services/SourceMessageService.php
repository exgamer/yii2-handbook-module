<?php

namespace concepture\yii2handbook\services;

use concepture\yii2logic\services\Service;

/**
 * Сервис для работы с оригиналами переводов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SourceMessageService extends Service
{
    /**
     * @var string класс перечисления словарей для переводов
     */
    public $messageCategoryEnumClass = '\concepture\yii2handbook\enum\MessageCategoryEnum';

    /**
     * Копирует исходных сообщений + переводы c категорией $from в категорию $to
     * ищет по массиву исходных сообщений $messages
     *
     * @param string $from
     * @param string $to
     * @param array $sourceMessages
     */
    public function copyCategory(string $from, string $to, array $sourceMessages, string $sourceMessageTable, string $messageTable)
    {

    }
}