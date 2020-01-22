<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042456__entity_position_modify extends Migration
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
        $this->dropIndex('uni_alias_domain_id_entity_type_position', $this->getTableName());
        $this->dropIndex('ind_domain_id_entity_type_position', $this->getTableName());
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