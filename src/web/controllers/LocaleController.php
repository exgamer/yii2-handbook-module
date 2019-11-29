<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;
/**
 * Class LocaleController
 * @package concepture\yii2handbook\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleController extends Controller
{
    protected function getAccessRules()
    {
        return [
            [
                'actions' => ['index', 'view','create', 'update', 'status-change'],
                'allow' => true,
                'roles' => [UserRoleEnum::ADMIN],
            ]
        ];
    }


    public function actions()
    {
        $actions = parent::actions();

        return array_merge($actions,[
            'status-change' => StatusChangeAction::class
        ]);
    }
}
