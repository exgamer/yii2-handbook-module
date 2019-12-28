<?php
namespace concepture\yii2handbook\models\traits;

use concepture\yii2handbook\models\Currency;

/**
 * Trait CurrencyTrait
 * @package concepture\yii2handbook\models\traits
 */
trait CurrencyTrait
{
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency']);
    }

    public function getCurrencyIso()
    {
        if (isset($this->currency)){
            return $this->currency->iso;
        }

        return null;
    }
}

