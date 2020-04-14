<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200414_051316_sitemap_fix
 */
class m200414_051316_sitemap_fix extends Migration
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
        $this->removeIndex('uni_entity_type_id_entity_id_sitemap');
        $this->addUniqueIndex(['entity_type_id', 'entity_id', 'domain_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_051316_sitemap_fix cannot be reverted.\n";

        return false;
    }
}
