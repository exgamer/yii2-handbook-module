<?php

namespace concepture\yii2handbook\models;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Class CurrencyLocalization
 *
 * @package common\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class CurrencyLocalization extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'currency_localization';
    }
}
