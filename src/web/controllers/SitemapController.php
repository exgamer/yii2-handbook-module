<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2handbook\enum\SitemapGeneratorEnum;
use concepture\yii2handbook\forms\SitemapForm;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use Yii;
use concepture\yii2handbook\enum\SitemapTypeEnum;
use concepture\yii2handbook\search\SitemapSearch;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\actions\web\StatusChangeAction;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Class SitemapController
 * @package concepture\yii2handbook\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapController extends Controller
{
    use ControllerTrait;

    /**
     * @inheritDoc
     */
    protected function getAccessRules()
    {
        return [
            [
                'actions' => [
                    'index',
                    'create',
                    'update',
                    'delete',
                ],
                'allow' => true,
                'roles' => [
                    UserRoleEnum::ADMIN
                ],
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);

        return $actions;
    }

    public function actionIndex()
    {
        $searchModel = new SitemapSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel->type = SitemapTypeEnum::CUSTOM;
        $searchModel->is_deleted = IsDeletedEnum::NOT_DELETED;
        $dataProvider =  $this->getService()->getDataProvider([], [], $searchModel);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new SitemapForm();
        $model->section = SitemapGeneratorEnum::DEFAULT_SECTION_NAME;
        $model->type = SitemapTypeEnum::CUSTOM;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if (($result = $this->getService()->create($model)) !== false) {
                if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {

                    return $this->redirect('index');
                } else {

                    return $this->redirect(['update', 'id' => $result->id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $originModel = $this->getModel($id);
        if (!$originModel){
            throw new NotFoundHttpException();
        }

        $model = new SitemapForm();
        $model->setAttributes($originModel->attributes, false);
        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if ($this->getService()->update($model, $originModel) !== false) {
                    if (Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        return $this->redirect('index');
                    }
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render('update', [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        $this->getService()->delete($model);

        return $this->redirect('index');
    }

    /**
     * Возвращает модель для редактирования
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->getOneByCondition([
            'id' => $id,
            'type' => SitemapTypeEnum::CUSTOM,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
        ]);
    }
}
