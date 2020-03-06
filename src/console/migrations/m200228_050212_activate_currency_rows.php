<?php

use yii\db\Migration;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class m200228_050212_activate_currency_rows
 */
class m200228_050212_activate_currency_rows extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('currency', ['status' => StatusEnum::ACTIVE], ['status' => StatusEnum::INACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200228_050212_activate_currency_rows cannot be reverted.\n";

        return false;
    }
}
