<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;

/**
 * Локали
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleController extends Controller
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
                        'status-change'
                    ],
                    'allow' => true,
                    'roles' => [
                        UserRoleEnum::ADMIN
                    ],
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);

        return array_merge(
            $actions,
            [
                'status-change' => [
                    'class' => StatusChangeAction::class,
                    'redirect' => false
                ]
            ]
        );
    }
}
