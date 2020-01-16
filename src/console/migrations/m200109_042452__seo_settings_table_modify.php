<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m191121_034605__seo_table_init
 */
class m200109_042452__seo_settings_table_modify extends Migration
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
        try {
            $this->dropForeignKey('fk_seo_settings_domain_id_domain_id', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropForeignKey('fk_seo_settings_locale_locale_id', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropIndex('ind_locale_seo_settings', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropIndex('ind_url_seo_settings', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropIndex('ind_domain_id_seo_settings', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropIndex('uni_url_md5_hash_locale_seo_settings', $this->getTableName());
        }catch (Exception $e){

        }

        try {
            $this->dropIndex('ss_url_md5_hash_index', $this->getTableName());
        }catch (Exception $e){

        }

        $this->renameTable($this->getTableName(), "{$this->getTableName()}_old");
        $this->addTable([
            'id' => $this->bigPrimaryKey(),
            'domain_id' => $this->bigInteger(),
            'locale' => $this->bigInteger(),
            'url' => $this->string(1024),
            'url_md5_hash' => $this->string(32),
            'name' => $this->string(512),
            'value' => $this->text(),
            'caption' => $this->string(512),
            'type' => $this->smallInteger(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()")),
            'updated_at' => ($this->isMysql() ? $this->dateTime()->append('ON UPDATE NOW()') : $this->dateTime()),
        ]);

        $this->addIndex(['locale']);
        $this->addIndex(['url']);
        $this->addIndex(['domain_id']);
        $this->addUniqueIndex(['url_md5_hash', 'name']);
        if ($this->isMysql()) {
            $this->execute("ALTER TABLE seo_settings
            ADD INDEX url_md5_hash_index
            USING HASH (url_md5_hash);");
        }
        if ($this->isPostgres()) {
            $this->execute("CREATE INDEX url_md5_hash_index 
            ON seo_settings USING HASH (url_md5_hash);");
        }
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
}
