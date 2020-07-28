<?php

namespace concepture\yii2handbook\models;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Class CountryLocalization
 *
 * @package common\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class CountryLocalization extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'country_localization';
    }
}
