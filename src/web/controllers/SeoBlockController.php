<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;
use concepture\yii2logic\actions\web\UndeleteAction;
use kamaelkz\yii2admin\v1\actions\EditableColumnAction;
use kamaelkz\yii2admin\v1\actions\SortAction;

/**
 * Контроллер сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockController extends Controller
{
    /**
     * @inheritDoc
     */
	protected function getAccessRules()
    {
        $rules = parent::getAccessRules();

        return ArrayHelper::merge(
            $rules,
            [
                [
                    'actions' => [
                        'undelete',
                        'status-change',
                        EditableColumnAction::actionName(),
                        SortAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        UserRoleEnum::ADMIN
                    ],
                ],
            ],
        );
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();

        return ArrayHelper::merge(
            $actions,
            [
                'status-change' => [
                    'class' => StatusChangeAction::class,
                    'redirect' => false,
                ],
                'undelete' => UndeleteAction::class,
                EditableColumnAction::actionName() => EditableColumnAction::class,
                SortAction::actionName() => SortAction::class,
            ]
        );
    }
}