<?php
use yii\db\Migration;
use concepture\yii2handbook\forms\LocaleForm;


/**
 * Class m190807_081012_add_default_locale
 */
class m190807_081012_add_default_locale extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $form = new LocaleForm();
        $form->locale = "ru";
        $form->caption = "Русский";
        $form->status = 1;
        $form->sort = 0;
        Yii::$app->localeService->create($form);
        $form = new LocaleForm();
        $form->locale = "en";
        $form->caption = "English";
        $form->status = 0;
        $form->sort = 0;
        Yii::$app->localeService->create($form);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190807_081012_add_default_locale cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190807_081012_add_default_locale cannot be reverted.\n";

        return false;
    }
    */
}
