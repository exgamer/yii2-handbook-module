<?php
namespace concepture\yii2handbook\models\traits;

use concepture\yii2handbook\models\PaymentSystem;

/**
 * Trait PaymentSystemTrait
 * @package concepture\yii2handbook\models\traits
 */
trait PaymentSystemTrait
{
    public function getPaymentSystem()
    {
        return $this->hasOne(PaymentSystem::className(), ['id' => 'payment_system_id']);
    }

    public function getPaymentSystemCaption()
    {
        if (isset($this->paymentSystem)){
            return $this->paymentSystem->caption;
        }

        return null;
    }
}

