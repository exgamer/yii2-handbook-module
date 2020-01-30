<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\actions\web\StatusChangeAction;
use concepture\yii2logic\actions\web\UndeleteAction;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;

/**
 * @deprecated static file type robots use
 *
 * Контроллер индексных файлов - robots.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RobotsController extends Controller
{
    /**
     * @inheritDoc
     */
    protected function getAccessRules()
    {
        return [
            [
                'actions' => [
                    'index',
                    'view',
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

        return array_merge($actions,[
            'status-change' => StatusChangeAction::class,
            'undelete' => UndeleteAction::class,
        ]);
    }
}
