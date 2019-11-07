<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191107_121704__settings_table_create
 */
class m191107_121704__settings_table_create extends Migration
{
    function getTableName()
    {
        return 'settings';
    }


    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->primaryKey(),
            'domain_id' => $this->integer(),
            'locale' => $this->integer(),
            'name' => $this->string(1024),
            'value' => $this->string(1024),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
        ]);
        $this->addIndex(['domain_id']);
        $this->addIndex(['locale']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191107_121704__settings_table_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191107_121704__settings_table_create cannot be reverted.\n";

        return false;
    }
    */
}
