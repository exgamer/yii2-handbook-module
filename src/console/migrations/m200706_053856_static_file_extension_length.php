<?php

use yii\db\Migration;

/**
 * Class m200706_053856_static_file_extension_length
 */
class m200706_053856_static_file_extension_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200706_053856_static_file_extension_length cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200706_053856_static_file_extension_length cannot be reverted.\n";

        return false;
    }
    */
}
