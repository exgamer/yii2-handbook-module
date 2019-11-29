<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m190807_080146_init_locales_table
 */
class m190807_080146_init_locales_table extends Migration
{
    function getTableName()
    {
        return 'locale';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'locale' => $this->string(2)->notNull(),
            'caption' => $this->string(100)->notNull(),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
        ]);
        $this->addIndex(['sort']);
        $this->addIndex(['status']);
        $this->addUniqueIndex(['locale']);
    }
}
