<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200911_065638_change_comment_updated_at_type
 */
class m200911_065638_change_comment_updated_at_type extends Migration
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'comment';
    }

    /**
     * @return bool|void
     */
    public function up()
    {
        $this->alterColumn($this->getTableName(),'updated_at', $this->dateTime()->defaultValue(new \yii\db\Expression("NULL ON UPDATE CURRENT_TIMESTAMP")));
    }

    /**
     * @return bool
     */
    public function down()
    {
        echo "m200728_055305_locale_declination_format cannot be reverted.\n";

        return false;
    }
}
