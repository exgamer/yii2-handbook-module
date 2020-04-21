<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200420_113315_create_currency_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200420_113315_create_currency_localization_table extends Migration
{
    function getTableName()
    {
        return 'currency_localization';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            $this->getTableName(),
            [
                'entity_id' => $this->bigInteger()->notNull(),
                'locale' => $this->bigInteger()->notNull(),
                'name' => $this->string(100)->notNull(),
                'symbol' => $this->string(10)->notNull(),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );
        $this->addPK(['entity_id', 'locale'], true);
        $this->addIndex(['entity_id']);
        $this->addIndex(['locale']);
        $this->addForeign('entity_id', 'currency','id');
        $this->addForeign('locale', 'locale','id');

        $this->dropColumn('currency', 'name');
        $this->dropColumn('currency', 'symbol');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
