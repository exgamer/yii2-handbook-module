<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042465__sitemap_table_fix
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042465__sitemap_table_fix extends Migration
{
    function getTableName()
    {
        return 'sitemap';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createColumn("controller_id", $this->string(255));
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