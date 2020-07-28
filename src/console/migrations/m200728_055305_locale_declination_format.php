<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200728_055305_locale_declination_format
 */
class m200728_055305_locale_declination_format extends Migration
{

    public function getTableName()
    {
        return 'locale';
    }

    public function up()
    {
        $this->addColumn($this->getTableName(), 'declination_format', $this->tinyInteger(1)->defaultValue(1));
    }

    public function down()
    {
        echo "m200728_055305_locale_declination_format cannot be reverted.\n";

        return false;
    }
}
