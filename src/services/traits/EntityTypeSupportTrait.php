<?php
namespace concepture\yii2handbook\services\traits;

use Yii;
use concepture\yii2logic\forms\Model;

/**
 * Trait EntityTypeSupportTrait
 * @package concepture\yii2handbook\services\traits
 */
trait EntityTypeSupportTrait
{
    /**
     * Возвращает ID сущности из справочника entity_type
     *
     * @return integer|null
     */
    public function getEntityTypeId()
    {
        $modelClass = $this->getRelatedModelClass();
        $tableName = $modelClass::tableName();

        return Yii::$app->entityTypeService->catalogKey($tableName);
    }
}

