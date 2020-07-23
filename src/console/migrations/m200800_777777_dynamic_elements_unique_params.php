<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Динамические элементы параметры атрибуты
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200800_777777_dynamic_elements_unique_params extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'dynamic_elements_v2';
    }

    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->addColumn($this->getTableName(), 'unique_params', 'text');
        $this->addColumn($this->getTableName(), 'unique_params_hash', 'varchar(32)');
        $sql = "CREATE INDEX index_unique_params_hash_{$this->getTableName()} ON {$this->getTableName()} (`unique_params_hash`) USING HASH";
        $this->execute($sql);
    }
}