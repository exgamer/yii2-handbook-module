<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2handbook\services\EntityTypePositionService;
use concepture\yii2handbook\forms\EntityTypePositionForm;

/**
 * Контроллер позиций сущностей на сайте
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionController extends Controller
{
    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions =  parent::actions();
        unset($actions['create']);

        return $actions;
    }

    /**
     * @return EntityTypePositionService
     */
    public function getService()
    {
        return Yii::$app->entityTypePositionService;
    }

    /**
     * Создание
     *
     * @param integer|null $entity_type_id
     *
     * @return string HTML
     */
    public function actionCreate($entity_type_id = null)
    {
        $model = Yii::createObject(EntityTypePositionForm::class);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if($entity_type_id) {
            $model->entity_type_id = $entity_type_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->create($model)) !== false) {
                if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                    return $this->redirect(['index', 'id' => $result->id]);
                } else {
                    return $this->redirect(['update', 'id' => $result->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'entity_type_id' => $entity_type_id,
        ]);
    }
}
