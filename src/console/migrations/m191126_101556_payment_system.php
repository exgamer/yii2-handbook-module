<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191126_101556_payment_system
 */
class m191126_101556_payment_system extends Migration
{
    function getTableName()
    {
        return 'payment_system';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'caption' => $this->string(20)->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
        ]);
        $this->addIndex(['status']);
    }
}
