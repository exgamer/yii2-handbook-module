<?php

namespace concepture\yii2handbook\models;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Class LocaleLocalization
 *
 * @package common\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class LocaleLocalization extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'locale_localization';
    }
}
