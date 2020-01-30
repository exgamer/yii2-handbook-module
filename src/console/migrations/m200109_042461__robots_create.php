<?php

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use concepture\yii2handbook\traits\ServicesTrait;
use yii\db\Migration;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;


/**
 * Class m200109_042461__robots_create
 */
class m200109_042461__robots_create extends Migration
{
    use ServicesTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $oldRobots = Yii::$app->robotsService->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED
        ]);

        $form = new StaticFileForm();
        $form->filename = "robots";
        $form->extension = FileExtensionEnum::TXT;
        $form->type = StaticFileTypeEnum::ROBOTS;
        $form->status = 1;
        if ($oldRobots){
            $form->content = $oldRobots->content;
        }

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
