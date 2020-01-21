<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042455__entity_position_init extends Migration
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
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'caption' => $this->string(255)->notNull(),
            'alias' => $this->string(255)->notNull(),
            'entity_type_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'domain_id' => $this->bigInteger(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => ($this->isMysql() ? $this->dateTime()->append('ON UPDATE NOW()') : $this->dateTime()),
        ]);
        $this->addIndex(['status']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['entity_type_id']);
        $this->addUniqueIndex(['alias', 'domain_id']);
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