<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2logic\actions\web\AutocompleteListAction;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\UndeleteAction;

/**
 * Class TagsController
 * @package concepture\yii2handbook\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsController extends Controller
{
    protected function getAccessRules()
    {
        return [
            [
                'actions' => ['index', 'create','update', 'view','delete', 'undelete', 'list'],
                'allow' => true,
                'roles' => [UserRoleEnum::ADMIN],
            ]
        ];
    }

    public function actions()
    {
        $actions = parent::actions();

        return array_merge($actions,[
            'undelete' => UndeleteAction::class,
            'list' => AutocompleteListAction::class,
        ]);
    }
}
