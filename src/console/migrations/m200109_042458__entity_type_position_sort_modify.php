<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042458__entity_type_position_sort_modify extends Migration
{
    function getTableName()
    {
        return 'entity_type_position_sort';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->getTableName(), 'entity_type_id', $this->bigInteger()->notNull()->defaultValue(0));
        $this->createIndex(
            "unique_" . $this->getTableName(),
            '{{%'.$this->getTableName().'}}',
            [
                'entity_id',
                'entity_type_id',
                'entity_type_position_id',
                'domain_id'
            ],
            true);
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