<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200429_083938_create_payment_system_localization_table
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200429_083938_create_payment_system_localization_table extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'payment_system_localization';
    }

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->getTableName(),
            [
                'entity_id' => $this->bigInteger()->notNull(),
                'locale' => $this->bigInteger()->notNull(),
                'name' => $this->string(1024)->notNull(),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );
        $this->addPK(['entity_id', 'locale'], true);
        $this->addIndex(['entity_id']);
        $this->addIndex(['locale']);
        $this->addForeign('entity_id', 'payment_system','id');
        $this->addForeign('locale', 'locale','id');
    }
}