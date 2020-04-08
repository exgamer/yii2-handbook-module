<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2user\enum\UserRoleEnum;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;
use concepture\yii2logic\actions\web\StatusChangeAction;
use concepture\yii2logic\actions\web\UndeleteAction;
use kamaelkz\yii2admin\v1\actions\EditableColumnAction;
use kamaelkz\yii2admin\v1\actions\SortAction;
use yii\helpers\ArrayHelper;

/**
 * Контроллер сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockController extends Controller
{
    use ControllerTrait;

    /**
     * @inheritDoc
     */
	protected function getAccessRules()
    {
        return ArrayHelper::merge(
            parent::getAccessRules(),
            [
                [
                    'actions' => [
                        'index',
                        'view',
                        'create',
                        'update',
                        'delete',
                        'undelete',
                        'status-change',
                        EditableColumnAction::actionName(),
                        SortAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [UserRoleEnum::ADMIN],
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

        return ArrayHelper::merge($actions,[
            'status-change' => StatusChangeAction::class,
            'undelete' => UndeleteAction::class,
            EditableColumnAction::actionName() => EditableColumnAction::class,
            SortAction::actionName() => SortAction::class,
		]);
    }


}
