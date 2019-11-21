<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191107_114223__comment_entity_types_table_create
 */
class m191107_114223__entity_types_table_create extends Migration
{
    function getTableName()
    {
        return 'entity_type';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'table_name' =>$this->string(1024)->notNull(),
            'caption' => $this->string(1024)->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
        ]);
        $this->addIndex(['status']);
        $this->addIndex(['table_name'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191107_114223__comment_entity_types_table_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191107_114223__comment_entity_types_table_create cannot be reverted.\n";

        return false;
    }
    */
}
