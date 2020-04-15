<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;
use concepture\yii2logic\actions\web\UndeleteAction;

/**
 * Статические файлы
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileController extends Controller
{
//    /**
//     * @inheritDoc
//     */
//    protected function getAccessRules()
//    {
//        return ArrayHelper::merge(
//            parent::getAccessRules(),
//            [
//                [
//                    'actions' => [
//                        'undelete',
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
        unset($actions['view']);

        return array_merge(
            $actions,
            [
                'status-change' => [
                    'class' => StatusChangeAction::class,
                    'redirect' => false
                ],
                'undelete' => UndeleteAction::class,
            ]
        );
    }
}
