<?php

use concepture\yii2handbook\forms\CountryForm;
use yii\db\Migration;


/**
 * Class m200108_081446_add_default_country
 */
class m200108_081446_add_default_country extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $form = new CountryForm();
//        $form->locale = Yii::$app->localeService->catalogKey('ru');
//        $form->iso = "ru";
//        $form->caption = "Россия";
//        $form->status = 1;
//        $form->sort = 0;
//        Yii::$app->countryService->create($form);
//        $form = new CountryForm();
//        $form->locale = Yii::$app->localeService->catalogKey('ru');
//        $form->iso = "kz";
//        $form->caption = "Казахстан";
//        $form->status = 0;
//        $form->sort = 0;
//        Yii::$app->countryService->create($form);
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
