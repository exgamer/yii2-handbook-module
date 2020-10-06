<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m201002_101809_sitemap_origin_domain
 */
class m201002_101809_sitemap_origin_domain extends Migration
{
    public function getTableName()
    {
        return 'sitemap';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createColumn('origin_domain_id', $this->bigInteger());
        $this->execute("UPDATE sitemap SET origin_domain_id=domain_id where domain_id > 0");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201002_101809_sitemap_origin_domain cannot be reverted.\n";

        return false;
    }
}
