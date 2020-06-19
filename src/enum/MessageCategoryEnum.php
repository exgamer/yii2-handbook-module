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
//    const ADMIN = 'yii2admin';
    const COMMON = 'common';

    /**
     * @inheritDoc
     */
    public static function labels()
    {
        return [
            self::FRONTEND => self::FRONTEND,
//            self::ADMIN => self::ADMIN,
            self::COMMON => self::COMMON,
        ];
    }
}