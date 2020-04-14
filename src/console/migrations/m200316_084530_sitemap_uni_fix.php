<?php

use yii\db\Migration;

/**
 * Class m200316_084530_sitemap_uni_fix
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200316_084530_sitemap_uni_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "ALTER TABLE sitemap DROP INDEX uni_entity_type_id_entity_id_sitemap;";
        $this->execute($sql);
        $sql = "ALTER TABLE sitemap
                ADD UNIQUE KEY 
                uni_entity_type_id_entity_id_sitemap 
                (entity_type_id, entity_id, domain_id);";
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200316_084529_seo_block cannot be reverted.\n";

        return false;
    }
}
