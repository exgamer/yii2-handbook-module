<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class TagTypeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileTypeEnum extends Enum
{
    const CUSTOM = 0;
    const SITEMAP = 1;
    const ROBOTS = 2;

    public static function labels($exclude = [])
    {
        $result = [
            self::CUSTOM => Yii::t('handbook', "Дефолтный"),
            self::SITEMAP => Yii::t('handbook', "Карта саита"),
            self::ROBOTS => Yii::t('handbook', "robots.txt")
        ];
        if(! empty($exclude)) {
            $result = array_flip($result);
            $result = array_diff($result, $exclude);
            $result = array_flip($result);
        }

        return $result;
    }
}
