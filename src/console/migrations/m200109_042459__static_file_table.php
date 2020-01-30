<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042459__static_file_table
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042459__static_file_table extends Migration
{
    function getTableName()
    {
        return 'static_file';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain_id' => $this->bigInteger(),
            'type' => $this->smallInteger()->defaultValue(0),
            'filename' => $this->string(255)->notNull(),
            'content' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'),
            'extension' => $this->string(10)->notNull(),
            'is_hidden' => $this->smallInteger()->defaultValue(0),
            'status' => $this->smallInteger()->defaultValue(0),
            'is_deleted' => $this->smallInteger()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()"))
        ]);
        $this->addIndex(['status']);
        $this->addIndex(['domain_id']);
        $this->addIndex(['is_deleted']);
        $this->addForeign('domain_id', 'domain','id');
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