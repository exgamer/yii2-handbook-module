<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m201009_044815_entity_type_class
 */
class m201009_044815_entity_type_class extends Migration
{
    public function getTableName()
    {
        return 'entity_type';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createColumn('model_class', $this->string(512));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201009_044815_entity_type_class cannot be reverted.\n";

        return false;
    }
}
