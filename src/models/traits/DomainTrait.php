<?php
namespace concepture\yii2handbook\models\traits;

use concepture\yii2handbook\models\Domain;

/**
 * Trait DomainTrait
 * @package concepture\yii2handbook\models\traits
 */
trait DomainTrait
{
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    public function getDomainName()
    {
        if (isset($this->domain)){
            return $this->domain->domain;
        }

        return null;
    }
}

