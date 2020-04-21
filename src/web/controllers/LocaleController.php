<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\enum\ScenarioEnum;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;
use yii\web\NotFoundHttpException;

/**
 * Локали
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleController extends Controller
{
    use LocalizedTrait;

    public $localized = true;
//    /**
//     * @inheritDoc
//     */
//    protected function getAccessRules()
//    {
//        $rules = parent::getAccessRules();
//
//        return ArrayHelper::merge(
//            $rules,
//            [
//                [
//                    'actions' => [
//                        'status-change'
//                    ],
//                    'allow' => true,
//                    'roles' => [
//                        UserRoleEnum::ADMIN
//                    ],
//                ]
//            ]
//        );
//    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        return array_merge(
            $actions,
            [
                'status-change' => [
                    'class' => StatusChangeAction::class,
                    'redirect' => false
                ]
            ]
        );
    }

    public function actionCreate($locale = null)
    {
        $view = 'create';
        $redirect = 'index';
        $serviceMethod = 'create';
        $scenario = ScenarioEnum::INSERT;

        $localeId = $this->getConvertedLocale($locale);
        $model = $this->getForm();
        $model->scenario = $scenario;
        $model->locale_id = $localeId;

        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()  && !$this->isReload()) {
            if (($result = $this->getService()->{$serviceMethod}($model)) !== false) {
                if ( RequestHelper::isMagicModal()){
                    return $this->controller->responseJson([
                        'data' => $result,
                    ]);
                }
                if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                    return $this->redirect([$redirect, 'id' => $result->id, 'locale' => $localeId]);
                } else {
                    return $this->redirect(['update', 'id' => $result->id, 'locale' => $localeId]);
                }
            }
        }

        return $this->render($view, [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id, $locale = null)
    {
        $view = 'update';
        $redirect = 'index';
        $serviceMethod = 'update';
        $scenario = ScenarioEnum::UPDATE;
        $originModelNotFoundCallback = null;

        $originModel = $this->getModel($id, $locale);
        if (!$originModel){
            if (! $originModelNotFoundCallback) {
                throw new NotFoundHttpException();
            }

            if (is_callable($originModelNotFoundCallback)){
                return call_user_func($originModelNotFoundCallback, $this);
            }
        }

        $model = $this->getForm();
        $model->scenario = $scenario;
        $model->setAttributes($originModel->attributes, false);
//        $model->locale = $this->getConvertedLocale($locale);
//        $model->locale = $this->getLocale();
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)  && !$this->isReload()) {
                if (($result =$this->getService()->{$serviceMethod}($model, $originModel)) !== false) {
                    if ( RequestHelper::isMagicModal()){
                        return $this->controller->responseJson([
                            'data' => $result,
                        ]);
                    }
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        return $this->redirect([$redirect, 'id' => $originModel->id, 'locale' => $model->locale]);
                    }
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render($view, [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }

    /**
     * Возвращает локализованную сущность с учетом локали если текущей локализации нет атрибуты будут пустые
     *
     *
     * @param $id
     * @param null $locale
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id, $locale = null)
    {
        $originModelClass = $this->getService()->getRelatedModel();
        $originModelClass::setLocale($locale);
        $model = $this->getService()->findById($id);
        if (! $model){

            return $originModelClass::clearFind()->where(['id' => $id])->one();
        }

        return $model;
    }
}
