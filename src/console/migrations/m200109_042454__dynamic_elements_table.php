<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042454__dynamic_elements_table extends Migration
{
    function getTableName()
    {
        return 'dynamic_elements';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('seo_settings', $this->getTableName());
        $this->dropTable('seo_settings_old');
        $this->addColumn($this->getTableName(), 'is_general', $this->tinyInteger(1)->defaultValue(0));
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
