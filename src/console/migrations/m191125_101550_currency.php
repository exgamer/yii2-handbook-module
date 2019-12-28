<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191125_101550_currency
 */
class m191125_101550_currency extends Migration
{
    function getTableName()
    {
        return 'currency';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'iso' => $this->string(10)->notNull(),
            'caption' => $this->string(20)->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime(),
        ]);
        $this->addIndex(['status']);
        $this->addUniqueIndex(['iso']);
    }
}
