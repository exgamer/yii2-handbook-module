<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Перечисление форматов склонения для транслитера {n, plural, ...}
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DeclinationFormatEnum extends Enum
{
    const FULL = 1;
    const TWO = 2;
    const THREE = 3;

    /**
     * @inheritDoc
     */
    public static function labels()
    {
        return [
            self::FULL => Yii::t('handbook', 'Четыре формы'),
            self::TWO => Yii::t('handbook', 'Две формы'),
            self::THREE => Yii::t('handbook', 'Три формы'),
        ];
    }
}