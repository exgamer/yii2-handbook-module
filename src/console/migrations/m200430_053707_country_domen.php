<?php

use concepture\yii2logic\console\migrations\Migration;;

/**
 * Class m200430_053707_country_domen
 */
class m200430_053707_country_domen extends Migration
{
    /**
     * @return string
     */
    function getTableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createColumn('domain_id', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200430_053707_country_domen cannot be reverted.\n";

        return false;
    }
}
