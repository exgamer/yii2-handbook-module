<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042495_url_history_fix
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042496_dynamic_elements_modify extends Migration
{
    function getTableName()
    {
        return 'dynamic_elements';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('uni_url_md5_hash_name_seo_settings', $this->getTableName());
        $this->addUniqueIndex(['url_md5_hash', 'name', 'domain_id']);
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