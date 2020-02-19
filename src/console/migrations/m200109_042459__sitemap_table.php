<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042459__sitemap_table
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042459__sitemap_table extends Migration
{
    function getTableName()
    {
        return 'sitemap';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain_id' => $this->bigInteger(),
            'entity_type_id' => $this->bigInteger()->notNull(),
            'entity_id' => $this->bigInteger()->notNull(),
            'location' => $this->string(255)->notNull(),
            'section' => $this->string(255)->notNull(),
            'static_file_id' => $this->bigInteger(),
            'last_modified_dt' => $this->dateTime(),
            'status' => $this->smallInteger()->defaultValue(0),
            'is_deleted' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()"))
        ]);
        $this->addIndex(['status']);
        $this->addIndex(['entity_type_id']);
        $this->addIndex(['entity_id']);
        $this->addIndex(['static_file_id']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['is_deleted']);
        $this->addForeign('domain_id', 'domain','id');
        $this->addForeign('entity_type_id', 'entity_type','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191121_034605__seo_table_init cannot be reverted.\n";

        return false;
    }
}