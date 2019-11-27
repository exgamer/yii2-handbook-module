<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Класс перечисления для тегов системы
 *
 * Class TargetAttributeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagTypeEnum extends Enum
{
    const SYSTEM = 0;
    const CUSTOM = 1;

    public static function labels()
    {
        return [
            self::SYSTEM => Yii::t('handbook', "Системный"),
            self::CUSTOM => Yii::t('handbook', "Пользовательский")
        ];
    }
}
