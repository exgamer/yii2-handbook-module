<?php

namespace concepture\yii2handbook\v2\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class SettingsTypeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DynamicElementsTypeEnum extends Enum
{
    const TEXT = 0;
    const TEXT_AREA = 1;
    const TEXT_EDITOR = 2;
    const CHECKBOX = 3;

    public static function labels()
    {
        return [
            self::TEXT => Yii::t('handbook', "Текст"),
            self::TEXT_AREA => Yii::t('handbook', "Многострочный текст"),
            self::TEXT_EDITOR => Yii::t('handbook', "Текстовый редактор"),
            self::CHECKBOX => Yii::t('handbook', "Чекбокс"),
        ];
    }
}
