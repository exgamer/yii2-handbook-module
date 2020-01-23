<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042457__entity_type_position_sort extends Migration
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
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'entity_id' => $this->bigInteger()->notNull(),
            'entity_type_position_id' => $this->bigInteger()->notNull(),
            'domain_id' => $this->bigInteger(),
            'sort' => $this->integer()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => ($this->isMysql() ? $this->dateTime()->append('ON UPDATE NOW()') : $this->dateTime()),
        ]);
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