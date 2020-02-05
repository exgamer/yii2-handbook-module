<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use concepture\yii2handbook\enum\SitemapTypeEnum;
use concepture\yii2handbook\search\SitemapSearch;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\actions\web\StatusChangeAction;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;

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
}
