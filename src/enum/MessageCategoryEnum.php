<?php

namespace concepture\yii2handbook\enum;

use concepture\yii2logic\enum\Enum;

/**
 * Перечисление словарей для переводов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MessageCategoryEnum extends Enum
{
    const FRONTEND = 'frontend';
    const GENERAL = 'general';
    const ADMIN = 'yii2admin';
    const DE = 'de';
    const COMMON = 'common';

    /**
     * @inheritDoc
     */
    public static function labels()
    {
        return [
            self::FRONTEND => self::FRONTEND,
            self::ADMIN => self::ADMIN,
            self::COMMON => self::COMMON,
            self::GENERAL => self::GENERAL,
            self::DE => self::DE,
        ];
    }
}