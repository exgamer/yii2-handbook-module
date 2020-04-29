<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200429_083911_change_payment_system_table
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200429_083911_change_payment_system_table extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'payment_system';
    }

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->renameColumn($this->getTableName(), 'caption', 'name');
        $this->addColumn($this->getTableName(), 'logo', 'VARCHAR(1024) AFTER name');
        $this->addColumn($this->getTableName(), 'updated_at', 'DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
        $this->addColumn($this->getTableName(), 'is_deleted', 'SMALLINT(6) DEFAULT 0');
        $this->addColumn($this->getTableName(), 'sort', 'SMALLINT(6) DEFAULT 0');
        $this->addIndex(['is_deleted']);
    }
}
