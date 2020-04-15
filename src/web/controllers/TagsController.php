<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2logic\actions\web\AutocompleteListAction;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\UndeleteAction;

/**
 * Тэги
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsController extends Controller
{
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
//                        'undelete',
//                        'list'
//                    ],
//                    'allow' => true,
//                    'roles' => [UserRoleEnum::ADMIN],
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

        return array_merge(
            $actions,
            [
                'undelete' => UndeleteAction::class,
                # todo: не интуитивно
                'list' => AutocompleteListAction::class,
            ]
        );
    }
}
