<?php
namespace concepture\yii2handbook\models\traits;

use concepture\yii2handbook\models\EntityType;

/**
 * Trait EntityTypeTrait
 * @package concepture\yii2handbook\models\traits
 */
trait EntityTypeTrait
{
    public function getEntityType()
    {
        return $this->hasOne(EntityType::className(), ['id' => 'entity_type_id']);
    }

    public function getEntityName()
    {
        if (isset($this->entityType)){
            return $this->entityType->table_name;
        }

        return null;
    }
}

