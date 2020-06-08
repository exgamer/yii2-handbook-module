<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Динамические элементы доменные атрибуты
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200601_777777_dynamic_elements_property extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'dynamic_elements_property';
    }

    /**
     * @inheritDoc
     */
    public function up()
    {
        $this->createTable(
            $this->getTableName(),
            [
                'entity_id' => $this->bigInteger()->notNull(),
                'domain_id' => $this->bigInteger()->notNull(),
                'default' => $this->smallInteger(1)->defaultValue(0),
                'value' => $this->text(),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );
        $this->addPK(['entity_id', 'domain_id'], true);
        $this->addIndex(['entity_id']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['default']);
        $this->addForeign('entity_id', 'dynamic_elements_v2','id');
        $this->addForeign('domain_id', 'domain','id');
    }
}