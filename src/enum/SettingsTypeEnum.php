<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class SettingsTypeEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsTypeEnum extends Enum
{
    const TEXT = 0;
    const TEXT_AREA = 1;
    const FROALA = 2;

    public static function labels()
    {
        return [
            self::TEXT => Yii::t('handbook', "Текст"),
            self::TEXT_AREA => Yii::t('handbook', "Многострочный текст"),
            self::FROALA => Yii::t('handbook', "Фроала"),
        ];
    }
}
