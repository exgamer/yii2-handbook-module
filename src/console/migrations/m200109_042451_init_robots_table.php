<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200109_042451_init_robots_table extends Migration
{
    function getTableName()
    {
        return 'robots';
    }

    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain_id' => $this->bigInteger(),
            'content' => $this->text()->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
            'is_deleted' => $this->smallInteger()->defaultValue(0),
        ]);
        $this->addIndex(['status']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['is_deleted']);
        $this->addForeign('domain_id', 'domain','id');
    }

    public function safeDown()
    {
        $this->dropTable($this->getTableName());
    }
}
