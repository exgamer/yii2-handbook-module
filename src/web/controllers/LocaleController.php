<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;
use yii\helpers\ArrayHelper;

/**
 * Class LocaleController
 * @package concepture\yii2handbook\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleController extends Controller
{
    protected function getAccessRules()
    {
        return ArrayHelper::merge(
            parent::getAccessRules(),
            [
                [
                    'actions' => ['index', 'view','create', 'update', 'status-change'],
                    'allow' => true,
                    'roles' => [UserRoleEnum::ADMIN],
                ]
            ]
        );
    }


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);

        return array_merge($actions,[
            'status-change' => StatusChangeAction::class
        ]);
    }
}
