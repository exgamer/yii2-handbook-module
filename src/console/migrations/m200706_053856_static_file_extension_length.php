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
        $this->execute("ALTER TABLE `static_file` CHANGE `extension` `extension` VARCHAR(50) CHARSET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;");
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
