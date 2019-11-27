<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191125_101549_tags
 */
class m191125_101549_tags extends Migration
{
    function getTableName()
    {
        return 'tags';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger()->notNull(),
            'locale' => $this->bigInteger()->notNull(),
            'domain_id' => $this->bigInteger(),
            'type' => $this->smallInteger()->defaultValue(0),
            'caption' => $this->string(100)->notNull(),
            'description' => $this->string(255),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
            'is_deleted' => $this->smallInteger()->defaultValue(0),
        ]);
        $this->addIndex(['user_id']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['locale']);
        $this->addIndex(['type']);
        $this->addIndex(['is_deleted']);
        $this->addUniqueIndex(['caption']);
        $this->addForeign('user_id', 'user','id');
        $this->addForeign('domain_id', 'domain','id');
        $this->addForeign('locale', 'locale','id');
    }
}
