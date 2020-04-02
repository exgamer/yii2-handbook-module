<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Перечисление позиций сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockPositionEnum extends Enum
{
    const HEAD = 0;
    const BODY = 1;

    /**
     * @inheritDoc
     */
    public static function labels()
    {
        return [
            self::HEAD => Yii::t('handbook', "HEAD"),
            self::BODY => Yii::t('handbook', "BODY (внизу)")
        ];
    }
}
