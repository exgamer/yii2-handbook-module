<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\services\EntityTypePositionService;
use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2logic\services\traits\UpdateColumnTrait;
use concepture\yii2logic\services\interfaces\UpdateColumnInterface;
use concepture\yii2handbook\forms\EntityTypePositionSortForm;
use yii\helpers\ArrayHelper;
use concepture\yii2handbook\traits\ServicesTrait as HandbookService;

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
    use HandbookService;

    /**
     * @return EntityTypePositionService
     */
    private function getEntityTypePositionService()
    {
        return Yii::$app->entityTypePositionService;
    }

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        # todo првоерять кол-во элементов
        $position = $this->getEntityTypePositionService()->getOneByCondition(['id' => $form->entity_type_position_id]);
        if(! $position) {
            throw new EntityTypePositionSortServiceException(Yii::t('handbook', 'Позиция не найдена'));
        }

        $maxCount = $position->max_count;
        $itemCount = $this->getItemsCount($form->entity_type_position_id, $form->domain_id);
        if($itemCount >= $maxCount) {
            throw new EntityTypePositionSortServiceException(Yii::t('handbook', 'Максимальное кол-во элементов в позиции - {count}', ['count' => $maxCount]));
        }
        # находит максимальное число сортировки и инкрементируем
        $maxSortValue = $this->getMaxSortValue($form->entity_type_position_id, $form->domain_id);
        if($maxSortValue) {
            $form->sort = ($maxSortValue + 1);
        }

        parent::beforeCreate($form);
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
            $query->andWhere([
                '`entity_type_position`.entity_type_id' => $entity_type_id,
                'entity_type_position_id' => $entity_type_position_id
            ]);
            $query->innerJoinWith('entityTypePosition');
            $query->orderBy('sort');
            $query->asArray();
        });

        return $items;
    }

    /**
     * @param ActiveQuery $query
     * @param string $entityTableName
     * @param string $entity_type_position
     * @param integer $entity_type_id
     * @param array $orderBy
     * @throws \ReflectionException
     */
    public function applyQuery(ActiveQuery $query, $entityTableName, $entity_type_id, $entity_type_position, $orderBy = [])
    {
        # todo: кеширровать
        $position = $this->getEntityTypePositionService()->getOneByCondition(
            [
                'alias' => $entity_type_position,
                'entity_type_id' => $entity_type_id
            ],
            true
        );
        if(! $position) {
            throw new EntityTypePositionSortServiceException('Position is not found.');
        }

        $domain = $this->domainService()->getCurrentDomain();
        $positionSort = $this->getTableName();
        $query->addSelect(["IFNULL({$positionSort}.sort, 9999) as sort"]);
        $query->join(
            'LEFT JOIN',
            $positionSort,
            "{$positionSort}.entity_id = {$entityTableName}.id
            AND {$positionSort}.entity_type_id =  {$entity_type_id}
            AND {$positionSort}.entity_type_position_id = {$position->id}
            " . ($domain ? "AND {$positionSort}.domain_id = {$domain->id}" : null)
        );
        $order = ArrayHelper::merge(["sort" => SORT_ASC], $orderBy);
        $query->orderBy($order);
    }

    /**
     * Максимальное значение сортировки
     *
     * @param integer $entity_type_position_id
     * @param integer $domain_id
     * @return integer
     */
    private function getMaxSortValue($entity_type_position_id, $domain_id)
    {
        $sql = "
                SELECT max(sort) 
                FROM {$this->getTableName()} 
                WHERE entity_type_position_id = {$entity_type_position_id}
        ";
        if ($domain_id) {
            $sql .= "AND domain_id = {$domain_id}";
        }else{
            $sql .= "AND domain_id IS NULL";
        }

        return $this->getDb()->createCommand($sql)->queryScalar();
    }

    /**
     * Количество элементов
     *
     * @param integer $entity_type_position_id
     * @param integer $domain_id
     * @return integer
     */
    private function getItemsCount($entity_type_position_id, $domain_id)
    {
        $sql = "
                SELECT count(sort) 
                FROM {$this->getTableName()} 
                WHERE entity_type_position_id = {$entity_type_position_id}
        ";
        if ($domain_id) {
            $sql .= "AND domain_id = {$domain_id}";
        }else{
            $sql .= "AND domain_id IS NULL";
        }

        return $this->getDb()->createCommand($sql)->queryScalar();
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortServiceException extends \Exception
{

}