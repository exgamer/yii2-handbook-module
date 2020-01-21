<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m191107_114224__entity_types_sort_module extends Migration
{
    function getTableName()
    {
        return 'entity_type';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->getTableName(), 'sort_module', $this->tinyInteger(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191107_114223__comment_entity_types_table_create cannot be reverted.\n";

        return false;
    }
}
