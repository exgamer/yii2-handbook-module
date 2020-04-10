<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use concepture\yii2handbook\services\EntityTypePositionSortService;
use concepture\yii2logic\filters\AjaxFilter;
use kamaelkz\yii2admin\v1\enum\FlashAlertEnum;

/**
 * Контроллер сортировки сущностей по позиции на сайте
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortController extends Controller
{
    /**
     * @return EntityTypePositionSortService
     */
    public function getService()
    {
        return Yii::$app->entityTypePositionSortService;
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['create'], $actions['update'], $actions['view'], $actions['delete']);

        return $actions;
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['onlyAjax'] = [
            'class' => AjaxFilter::class,
            'except' => [
                'options'
            ],
        ];

        return $behaviors;
    }

    /**
     * Создание
     *
     * @param integer $entity_id
     * @param integer $entity_type_id
     * @param integer $entity_type_position_id
     *
     * @return string HTML
     */
    public function actionCreate($entity_id, $entity_type_id, $entity_type_position_id)
    {
        $model = $this->getService()->getRelatedForm();
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        $model->entity_id = $entity_id;
        $model->entity_type_id = $entity_type_id;
        $model->entity_type_position_id = $entity_type_position_id;

        if ($model->validate()) {
            try {
                if (($result = $this->getService()->create($model)) !== false) {

                    return $this->responseNotify();
                }
            } catch (\Exception $e) {
                return $this->responseNotify(FlashAlertEnum::WARNING, $e->getMessage());
            }
        }

        return $this->responseNotify(FlashAlertEnum::WARNING, $this->getErrorFlash());
    }

    /**
     * Удаление записи
     *
     * @param integer $id
     * @return array
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $item = $this->getService()->findById($id);
        if (! $item){
            throw new NotFoundHttpException();
        }

        try {
            $this->getService()->delete($item);

            return $this->responseNotify();
        } catch (\Exception $e) {
            return $this->responseNotify(FlashAlertEnum::WARNING, $e->getMessage());
        }
    }
}
