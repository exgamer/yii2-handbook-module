<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200723_081357_dynamic_elements_hint
 */
class m200723_081357_dynamic_elements_hint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 'dynamic_elements_v2';
    }

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn($this->getTableName(), 'hint', $this->text());
    }
}
