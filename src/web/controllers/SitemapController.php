<?php

namespace concepture\yii2handbook\web\controllers;

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
                    'undelete',
                    'status-change'
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
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);

        return array_merge($actions,[
            'status-change' => StatusChangeAction::class
        ]);
    }
}
