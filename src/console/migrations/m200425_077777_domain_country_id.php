<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Идентификатор страны для домена
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class m200425_077777_domain_country_id extends Migration
{
    /**
     * @inheritDoc
     */
    public function getTableName()
    {
        return 'domain';
    }

    /**
     * @inheritDoc
     */
    public function safeUp()
    {
        $this->addColumn($this->getTableName(), 'country_id', $this->bigInteger()->defaultValue(0));
        $this->addIndex(['country_id']);
    }
}
