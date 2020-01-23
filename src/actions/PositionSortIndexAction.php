<?php

namespace concepture\yii2handbook\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use concepture\yii2logic\actions\Action;
use concepture\yii2handbook\services\EntityTypeService;
use concepture\yii2handbook\services\EntityTypePositionService;
use concepture\yii2handbook\services\EntityTypePositionSortService;

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
     * @var array
     */
    public $entityColumns = [];

    /**
     * @var string
     */
    public $labelColumn;

    /**
     * Название дейстия
     *
     * @return string
     */
    public static function actionName()
    {
        return 'position-sort-index';
    }

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
     * @return EntityTypePositionSortService
     */
    public function getEntityTypePositionSortService()
    {
        return Yii::$app->entityTypePositionSortService;
    }

    /**
     * @inheritDoc
     */
    public function run($entity_type_position_id = null)
    {
        if(! $this->labelColumn) {
            throw new InvalidConfigException('Property `labelColumn` must be set');
        }

        $entityService = $this->getService();
        $entitySearchClass = $this->getSearchClass();
        $entitySearchModel = Yii::createObject($entitySearchClass);
        $this->extendSearch($entitySearchModel);
        $entitySearchModel->load(Yii::$app->request->queryParams);
        $tableName = trim($entityService->getTableName(), '{}');
        $entity_type = $this->getEntityTypeService()->getOneByCondition([
            'table_name' => $tableName,
            'sort_module' => true
        ]);
        if(! $entity_type) {
            throw new NotFoundHttpException('Entity type is not defined');
        }

        $entity_ids = [];
        $items = $this->getEntityTypePositionSortService()->getItemsAsArray($entity_type->id, $entity_type_position_id);
        if($items) {
            $entity_ids = ArrayHelper::getColumn($items, 'entity_id');
            $entities = $entityService->getAllByCondition(function(ActiveQuery $query) use($entity_ids) {
                $query->asArray();
                $query->indexBy('id');
                $query->andWhere(['id' => $entity_ids]);
            });

            foreach ($items as &$item) {
                $item['label'] = $entities[$item['entity_id']][$this->labelColumn] ?? null;
            }
        }

        $sortDataProvider = new ArrayDataProvider([
            'allModels' => $items,
            'pagination' => false,
        ]);
        $itemsIds = ArrayHelper::getColumn($items, 'id');
        $sortDataProvider->setKeys($itemsIds);
        $entityDataProvider = $entityService->getDataProvider([], [], $entitySearchModel, null, function(ActiveQuery $query) use($entity_ids) {
            if(! $entity_ids) {
                return;
            }

            $query->andWhere(['not in', 'id', $entity_ids]);
        });
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
            'entityColumns' => $this->entityColumns,
            'labelColumn' => $this->labelColumn,
        ]);
    }

    /**
     * Для доп обработки search модели
     * @param $searchModel
     */
    protected function extendSearch($searchModel){}
}