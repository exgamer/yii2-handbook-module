<?php

namespace concepture\yii2handbook\v2\enum;

use concepture\yii2logic\enum\Enum;

/**
 * Зарезервированные имена динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsNameEnum extends Enum
{
    # мета ключи
    const TITLE = 'TITLE';
    const DESCRIPTION = 'DESCRIPTION';
    const KEYWORDS = 'KEYWORDS';
    # часто используемые
    const HEADER = 'HEADER';
    const TEXT = 'TEXT';
    const TEXT_TOP = 'TEXT_TOP';
    const TEXT_BOTTOM = 'TEXT_BOTTOM';

    /**
     * Мета ключи
     *
     * @return array
     */
    public static function metaValues()
    {
        return [
            self::TITLE,
            self::DESCRIPTION,
            self::KEYWORDS,
        ];
    }
}
