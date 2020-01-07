<?php
namespace concepture\yii2handbook\services;

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
}