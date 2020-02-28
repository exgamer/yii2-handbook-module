<?php

use concepture\yii2handbook\forms\CurrencyForm;
use yii\db\Migration;


/**
 * Class m191125_101555_add_currency
 */
class m191125_101555_add_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $form = new CurrencyForm();
//        $form->iso = "RUR";
//        $form->caption = "Рубль";
//        $form->status = 1;
//        Yii::$app->currencyService->create($form);
//        $form = new CurrencyForm();
//        $form->iso = "KZT";
//        $form->caption = "Тенге";
//        $form->status = 1;
//        Yii::$app->currencyService->create($form);
//        $form = new CurrencyForm();
//        $form->iso = "USD";
//        $form->caption = "Американский доллар";
//        $form->status = 1;
//        Yii::$app->currencyService->create($form);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_081012_add_default_locale cannot be reverted.\n";

        return false;
    }
}
