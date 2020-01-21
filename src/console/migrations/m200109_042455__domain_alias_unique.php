<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042455__domain_alias_unique extends Migration
{
    function getTableName()
    {
        return 'domain';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addUniqueIndex(['alias']);
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
