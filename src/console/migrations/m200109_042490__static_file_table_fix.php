<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200109_042490__static_file_table_fix
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class m200109_042490__static_file_table_fix extends Migration
{
    function getTableName()
    {
        return 'static_file';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try{
            $this->removeIndex('uni_filename_extension_type_static_file');
        }catch (\Exception $ex){

        }

        $this->addUniqueIndex(['filename', 'extension', 'type', 'domain_id']);
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