<?php

namespace concepture\yii2handbook\services;

use yii\db\ActiveQuery;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2handbook\services\traits\EntityTypeSupportTrait;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServiceTrait;

/**
 * Class MenuService
 *
 * @package concepture\yii2handbook\services
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuService extends Service
{
    use StatusTrait;
    use ReadSupportTrait;
    use ModifySupportTrait;
    use HandbookServiceTrait;
    use EntityTypeSupportTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        parent::beforeCreate($form);
    }

    /**
     * @param string $entity_type_position
     * @return \Closure
     */
    public function getPositionSortCondition($entity_type_position)
    {
        return function(ActiveQuery $query) use($entity_type_position) {
            $query->addSelect('*');
            $entityTableName = trim($this->getTableName(), '{}');
            if(! $entity_type_position) {
                $query->orderBy(["{$entityTableName}.id" => SORT_DESC]);
            } else {
                $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $entityTableName], true);
                if(! $entity_type) {
                    throw new MenuServiceException('Entity type not found.');
                }

                $this->entityTypePositionSortService()->applyQuery(
                    $query,
                    $entityTableName,
                    $entity_type->id,
                    $entity_type_position,
                    [
                        "{$entityTableName}.id" => SORT_DESC,
                    ]
                );
            }

            $query->andWhere([
                "{$entityTableName}.status" => StatusEnum::ACTIVE,
                "{$entityTableName}.is_deleted" => IsDeletedEnum::NOT_DELETED,
            ]);
        };
    }
}

/**
 * Class MenuServiceException
 *
 * @package common\services
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuServiceException extends \Exception
{

}