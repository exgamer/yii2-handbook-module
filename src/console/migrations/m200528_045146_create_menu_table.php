<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200528_045146_create_menu_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200528_045146_create_menu_table extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'menu';
    }

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'type' => $this->integer()->notNull(),
            'caption' => $this->string(255)->notNull(),
            'url' => $this->string(1024),
            'desktop_max_count' => $this->bigInteger()->defaultValue(0),
            'link_all_caption' => $this->string(255),
            'link_all_url' => $this->string(1024),
            'items' => $this->json(),
            'domain_id' => $this->bigInteger()->notNull(),
            'status' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("CURRENT_TIMESTAMP")),
            'updated_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NULL ON UPDATE CURRENT_TIMESTAMP")),
            'is_deleted' => $this->smallInteger()->defaultValue(0),
            'sort' => $this->smallInteger()->defaultValue(0),
        ]);

        $this->addIndex(['type', 'domain_id', 'status', 'is_deleted']);
        $this->addForeign('domain_id', 'domain','id');
    }
}
