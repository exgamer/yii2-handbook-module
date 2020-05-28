<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200528_113142_remove_menu_sort
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200528_113142_remove_menu_sort extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'menu';
    }

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->removeColumn('sort');
    }
}
