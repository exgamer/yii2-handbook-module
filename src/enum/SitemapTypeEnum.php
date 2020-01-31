<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class SitemapTypeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapTypeEnum extends Enum
{
    const DYNAMIC = 0;
    const CUSTOM = 1;

    public static function labels($exclude = [])
    {
        return [
            self::DYNAMIC => Yii::t('handbook', "Динамический"),
            self::CUSTOM => Yii::t('handbook', "Статический"),
        ];
    }
}
