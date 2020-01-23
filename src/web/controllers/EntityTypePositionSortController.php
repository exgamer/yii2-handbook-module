<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use yii\db\ActiveQuery;
use concepture\yii2article\services\PostService;
use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Контроллер сортировки сущностей по позиции на сайте
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortController extends Controller
{
    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['update'], $actions['view']);

        return $actions;
    }
//
//    /**
//     * @return PostService
//     */
//    private function getPostService()
//    {
//        return Yii::$app->postService;
//    }
//
//    /**
//     * Интерфейс сортировки
//     *
//     * @param integer $entity_type_id
//     * @param integer $entity_type_position_id
//     *
//     * @return string HTML
//     */
//    public function actionIndex($entity_type_id = null, $entity_type_position_id = null)
//    {
//        $searchClass = $this->getPostService()->getRelatedSearchModelClass();
//        $searchModel = Yii::createObject($searchClass);
//        $searchModel->load(Yii::$app->request->queryParams);
//
//        $dataProvider =  $this->getPostService()->getDataProvider([], [], $searchModel, null, function(ActiveQuery $query) {
//            $query->andWhere([
//                'is_deleted' => IsDeletedEnum::NOT_DELETED,
//            ]);
//        });
//
//        $entityTypesPositions = [];
//        $entityTypes = Yii::$app->entityTypeService->getDropDownList();
//        if($entity_type_id) {
//            $entityTypesPositions = Yii::$app->entityTypePositionService->getDropDownList([], '', function (ActiveQuery $query) use ($entity_type_id) {
//                $query->andWhere(['entity_type_id' => $entity_type_id]);
//            });
//        }
//
//        return $this->render('index', [
//            'entity_type_id' => $entity_type_id,
//            'entity_type_position_id' => $entity_type_position_id,
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//            'entityTypes' => $entityTypes,
//            'entityTypesPositions' => $entityTypesPositions,
//        ]);
//    }
}
