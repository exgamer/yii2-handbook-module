<?php

namespace concepture\yii2handbook\enum;

use concepture\yii2logic\enum\Enum;

/**
 * Class MenuEnum
 *
 * @package concepture\yii2handbook\enum
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuEnum extends Enum
{
    /** @var int */
    const TYPE_HEADER = 1;
    /** @var int */
    const TYPE_FOOTER = 2;

    /**
     * @inheritDoc
     */
    public static function labels()
    {
        return [
            self::TYPE_HEADER => \Yii::t('handbook', "Верхнее меню"),
            self::TYPE_FOOTER => \Yii::t('handbook', "Нижнее меню"),
        ];
    }
}
