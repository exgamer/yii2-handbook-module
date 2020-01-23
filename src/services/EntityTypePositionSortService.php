<?php

namespace concepture\yii2handbook\services;

use yii\db\ActiveQuery;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2logic\services\traits\UpdateColumnTrait;
use concepture\yii2logic\services\interfaces\UpdateColumnInterface;

/**
 * Сервис для работы с
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortService extends Service implements UpdateColumnInterface
{
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;
    use UpdateColumnTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        # находит максимальное число сортировки и инкрементируем
        $sql = "
                SELECT max(sort) 
                FROM {$this->getTableName()} 
                WHERE entity_type_position_id = {$form->entity_type_position_id}
                AND domain_id = {$form->domain_id}
        ";
        $value = $this->getDb()->createCommand($sql)->queryScalar();
        if(! $value) {
            return;
        }

        $form->sort = ($value + 1);
    }

    /**
     * Метод для расширения find()
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * @param ActiveQuery $query
     * @see \concepture\yii2logic\services\Service::extendFindCondition()
     */
    protected function extendQuery(ActiveQuery $query)
    {
        $this->applyDomain($query);
    }

    /**
     * @param $entity_type_id
     * @param $entity_type_position_id
     * @return array
     */
    public function getItemsAsArray($entity_type_id, $entity_type_position_id)
    {
        if(! $entity_type_position_id) {
            return [];
        }

        $items = $this->getAllByCondition(function(ActiveQuery $query) use($entity_type_id, $entity_type_position_id) {
            $query->select(['entity_type_position_sort.id', 'entity_id', 'sort']);
            $query->andWhere([
                'entity_type_id' => $entity_type_id,
                'entity_type_position_id' => $entity_type_position_id
            ]);
            $query->innerJoinWith('entityTypePosition');
            $query->orderBy('sort');
            $query->asArray();
        });

        return $items;
    }
}