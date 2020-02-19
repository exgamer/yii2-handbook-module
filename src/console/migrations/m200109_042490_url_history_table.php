<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042490_url_history_table
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042490_url_history_table extends Migration
{
    function getTableName()
    {
        return 'url_history';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain_id' => $this->bigInteger(),
            'entity_type_id' => $this->bigInteger(),
            'entity_id' => $this->bigInteger(),
            'parent_id' => $this->bigInteger(),
            'location' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()"))
        ]);
        $this->addIndex(['entity_type_id']);
        $this->addIndex(['entity_id']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['parent_id']);
        $this->addForeign('domain_id', 'domain','id');
        $this->addForeign('entity_type_id', 'entity_type','id');
        $this->addForeign('parent_id', 'url_history','id');
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