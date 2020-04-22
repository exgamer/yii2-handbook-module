<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200422_081446_create_country_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200422_081446_create_country_localization_table extends Migration
{
    function getTableName()
    {
        return 'country_localization';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            $this->getTableName(),
            [
                'entity_id' => $this->bigInteger()->notNull(),
                'locale' => $this->bigInteger()->notNull(),
                'caption' => $this->string(100)->notNull(),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );
        $this->addPK(['entity_id', 'locale'], true);
        $this->addIndex(['entity_id']);
        $this->addIndex(['locale']);
        $this->addForeign('entity_id', 'country','id');
        $this->addForeign('locale', 'locale','id');

        $this->dropColumn('country', 'locale');
        $this->dropColumn('country', 'caption');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
