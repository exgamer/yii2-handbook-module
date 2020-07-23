<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Динамические элементы доменные атрибуты
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200700_777777_dynamic_elements_modify extends Migration
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
        $this->dropIndex('ind_name_route_hash_route_params_hash_dynamic_elements_v2', $this->getTableName());
    }
}