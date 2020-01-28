<?php

namespace concepture\yii2handbook\services;

use Yii;
use concepture\yii2logic\services\Service;

/**
 * Class EntityTypeService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityTypeService extends Service
{
    /**
     * @see \concepture\yii2logic\services\traits\CatalogTrait
     */
    protected function catalogKeyPreAction(&$value, &$catalog)
    {
        $value = trim($value, '{}');
    }

    /**
     * @inheritDoc
     *
     * @param bool $cache
     */
    public function getOneByCondition($condition = null, $cache = false)
    {
        if( ! Yii::$app->has('cache') || !$cache) {
            return parent::getOneByCondition($condition);
        }

        return $this->getDb()->cache(function ($condition) {
            return parent::getOneByCondition($condition);
        });
    }
}