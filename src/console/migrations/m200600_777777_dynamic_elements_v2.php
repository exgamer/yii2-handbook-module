<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Динамические элементы версия 2
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200600_777777_dynamic_elements_v2 extends Migration
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
        $this->createTable(
            $this->getTableName(),
            [
                'id' => $this->bigPrimaryKey(),
                'route' => $this->string(1024)->notNull(),
                'route_hash' => $this->string(32)->notNull(),
                'route_params' => $this->string(1024),
                'route_params_hash' => $this->string(32),
                'name' => $this->string(512)->notNull(),
                'caption' => $this->string(512)->notNull(),
                'type' => $this->smallInteger()->defaultValue(0),
                'general' => $this->smallInteger()->defaultValue(0),
                'multi_domain' => $this->smallInteger()->defaultValue(1),
                'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("CURRENT_TIMESTAMP")),
                'updated_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NULL ON UPDATE CURRENT_TIMESTAMP")),
                'is_deleted' => $this->smallInteger()->defaultValue(0),
                'sort' => $this->bigInteger()->defaultValue(0),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );

        $sql = "CREATE INDEX index_route_hash_{$this->getTableName()} ON {$this->getTableName()} (`route_hash`) USING HASH";
        $this->execute($sql);
        $sql = "CREATE INDEX index_route_params_hash_{$this->getTableName()} ON {$this->getTableName()} (`route_params_hash`) USING HASH";
        $this->execute($sql);
        $this->addIndex(['name', 'route_hash', 'route_params_hash'], true);
        $this->addIndex(['is_deleted']);
        $this->addIndex(['sort']);
        $this->addIndex(['type']);
        $this->addIndex(['general']);
        $this->addIndex(['multi_domain']);
    }
}