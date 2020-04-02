<?php

use yii\db\Migration;

/**
 * Таблица сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200316_084529_handbook_seo_block extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `seo_block` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `caption` varchar(255) COLLATE utf8mb4_bin NOT NULL,
            `position` tinyint(1) NOT NULL,
            `sort` int(11) DEFAULT NULL,
            `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
            `content` text COLLATE utf8mb4_bin NOT NULL,
            `status` tinyint(1) NOT NULL DEFAULT '1', 
            `domain_id` bigint(20) NOT NULL,
            `url` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '/',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
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
