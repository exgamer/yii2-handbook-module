<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200108_080147__country_table_modify
 */
class m200108_080147__country_table_modify extends Migration
{
    function getTableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createColumn("image", $this->string(1024));
        $this->createColumn("image_anons", $this->string(1024));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191121_034605__seo_table_init cannot be reverted.\n";

        return false;
    }
}
