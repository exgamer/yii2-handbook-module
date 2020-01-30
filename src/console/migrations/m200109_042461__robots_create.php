<?php

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use yii\db\Migration;


/**
 * Class m200109_042461__robots_create
 */
class m200109_042461__robots_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $form = new StaticFileForm();
        $form->filename = "robots";
        $form->extension = FileExtensionEnum::TXT;
        $form->type = StaticFileTypeEnum::ROBOTS;
        $form->status = 1;
        Yii::$app->staticFileService->create($form);
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
