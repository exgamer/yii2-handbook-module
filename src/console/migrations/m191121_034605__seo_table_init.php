<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191121_034605__seo_table_init
 */
class m191121_034605__seo_table_init extends Migration
{
    function getTableName()
    {
        return 'seo_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'locale' => $this->bigInteger()->notNull(),
            'domain_id' => $this->bigInteger(),
            'url' => $this->string(1024),
            'url_md5_hash' => $this->string(32),
            'seo_h1' => $this->string(1024),
            'seo_title' => $this->string(175),
            'seo_description' => $this->string(175),
            'seo_keywords' => $this->string(175),
            'seo_text' => $this->text(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => $this->dateTime()->append('ON UPDATE NOW()'),
        ]);
        $this->addIndex(['locale']);
        $this->addIndex(['url']);
        $this->addIndex(['domain_id']);
        $this->addUniqueIndex(['url_md5_hash', 'locale']);
        $this->execute("ALTER TABLE seo_settings
            ADD INDEX ss_url_md5_hash_index
            USING HASH (url_md5_hash);");
        $this->addForeign('locale', 'locale','id');
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191121_034605__seo_table_init cannot be reverted.\n";

        return false;
    }
    */
}
