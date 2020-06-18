<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Динамические элементы параметры атрибуты
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200702_777777_dynamic_elements_modify2 extends Migration
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
        $this->addColumn($this->getTableName(), 'value_params', 'text');
    }
}