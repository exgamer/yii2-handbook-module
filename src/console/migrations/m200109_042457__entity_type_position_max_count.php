<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042457__entity_type_position_max_count extends Migration
{
    function getTableName()
    {
        return 'entity_type_position';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->getTableName(), 'max_count', $this->integer()->notNull()->defaultValue(20));
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