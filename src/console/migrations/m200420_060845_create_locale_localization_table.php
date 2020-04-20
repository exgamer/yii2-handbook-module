<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200420_060845_create_locale_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200420_060845_create_locale_localization_table extends Migration
{
    function getTableName()
    {
        return 'locale_localization';
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
                'locale_id' => $this->bigInteger()->notNull(),
                'caption' => $this->string(100)->notNull(),
            ],
            'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB'
        );
        $this->addPK(['entity_id', 'locale_id'], true);
        $this->addIndex(['entity_id']);
        $this->addIndex(['locale_id']);
        $this->addForeign('entity_id', 'locale','id');
        $this->addForeign('locale_id', 'locale','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
