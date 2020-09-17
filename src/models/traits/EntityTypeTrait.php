<?php
namespace concepture\yii2handbook\models\traits;

use concepture\yii2handbook\models\EntityType;

/**
 * Trait EntityTypeTrait
 * @package concepture\yii2handbook\models\traits
 */
trait EntityTypeTrait
{
    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->hasOne(EntityType::class, ['id' => 'entity_type_id']);
    }

    /**
     * @return string|null
     */
    public function getEntityName()
    {
        if (isset($this->entityType)){
            return $this->entityType->table_name;
        }

        return null;
    }
    /**
     * @return string|null
     */
    public function getEntityCaption()
    {
        if (isset($this->entityType)){
            return $this->entityType->caption;
        }
        return null;
    }
}

