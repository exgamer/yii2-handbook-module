<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191106_101926__init_domain_table
 */
class m191106_101926__init_domain_table extends Migration
{
    function getTableName()
    {
        return 'domain';
    }

    public function up()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain' => $this->string(255)->notNull(),
            'description' => $this->string(1024),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
        ]);
        $this->addIndex(['domain']);
        $this->addIndex(['sort']);
        $this->addIndex(['status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191106_101926__init_domain_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191106_101926__init_domain_table cannot be reverted.\n";

        return false;
    }
    */
}
