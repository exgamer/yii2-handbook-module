<?php

namespace concepture\yii2handbook\models;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Class PaymentSystemLocalization
 *
 * @package concepture\yii2handbook\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 *
 * @property integer $entity_id
 * @property integer $locale
 * @property string $name
 */
class PaymentSystemLocalization extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'payment_system_localization';
    }
}
