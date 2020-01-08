<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200108_080146_init_country_table
 */
class m200108_080146_init_country_table extends Migration
{
    function getTableName()
    {
        return 'country';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'iso' => $this->string(2)->notNull(),
            'locale' => $this->bigInteger()->notNull(),
            'caption' => $this->string(100)->notNull(),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime(),
        ]);
        $this->addIndex(['sort']);
        $this->addIndex(['status']);
        $this->addUniqueIndex(['iso']);
        $this->addForeign('locale', 'locale','id');
    }
}
