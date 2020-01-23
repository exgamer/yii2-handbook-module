<?php

namespace concepture\yii2handbook\actions;

use Yii;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\actions\Action;
use concepture\yii2handbook\services\EntityTypeService;
use concepture\yii2handbook\services\EntityTypePositionService;

/**
 * Действие для сортировки сущностей по позициям
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class PositionSortIndexAction extends Action
{
    /**
     * @var string
     */
    public $view = '@concepture/yii2handbook/views/entity-type-position-sort/index';

    /**
     * @return EntityTypeService
     */
    public function getEntityTypeService()
    {
        return Yii::$app->entityTypeService;
    }

    /**
     * @return EntityTypePositionService
     */
    public function getEntityTypePositionService()
    {
        return Yii::$app->entityTypePositionService;
    }

    /**
     * @inheritDoc
     */
    public function run($entity_type_position_id = null)
    {
        $entityService = $this->getService();
        $entitySearchClass = $this->getSearchClass();
        $entitySearchModel = Yii::createObject($entitySearchClass);
        $this->extendSearch($entitySearchModel);
        $entitySearchModel->load(Yii::$app->request->queryParams);
        $entityDataProvider = $entityService->getDataProvider([], [], $entitySearchModel);
        $tableName = trim($entityService->getTableName(), '{}');
        $entity_type = $this->getEntityTypeService()->getOneByCondition([
            'table_name' => $tableName,
            'sort_module' => true
        ]);
        if(! $entity_type) {
            throw new NotFoundHttpException('Entity type is not defined');
        }

        $positionSearchClass = $this->getEntityTypePositionService()->getRelatedSearchModelClass();
        $positionSearchModel = Yii::createObject($positionSearchClass);
        $sortDataProvider = $this->getEntityTypePositionService()->getDataProvider([], [], $positionSearchModel);

        $positions = $this->getEntityTypePositionService()->getDropDownList([], '', function (ActiveQuery $query) use ($entity_type) {
            $query->andWhere(['entity_type_id' => $entity_type->id]);
        });

        return $this->render($this->view, [
            'entitySearchModel' => $entitySearchModel,
            'entityDataProvider' => $entityDataProvider,
            'sortDataProvider' => $sortDataProvider,
            'entity_type_id' => $entity_type->id,
            'entity_type_position_id' => $entity_type_position_id,
            'positions' => $positions,
        ]);
    }

    /**
     * Для доп обработки search модели
     * @param $searchModel
     */
    protected function extendSearch($searchModel){}
}