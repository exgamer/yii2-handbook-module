<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042453__seo_settings_table_modify extends Migration
{
    function getTableName()
    {
        return 'seo_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->getTableName(), 'url', 'VARCHAR(256)');
        $this->addIndex(['url', 'url_md5_hash']);
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
