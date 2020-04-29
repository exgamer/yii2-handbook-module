<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2logic\enum\AccessEnum;
use kamaelkz\yii2admin\v1\actions\SortAction;
use concepture\yii2logic\actions\web\v2\DeleteAction;
use kamaelkz\yii2admin\v1\controllers\BaseController;
use concepture\yii2logic\actions\web\v2\StatusChangeAction;
use concepture\yii2handbook\actions\PositionSortIndexAction;
use concepture\yii2handbook\services\EntityTypePositionSortService;

/**
 * Class PaymentSystemController
 *
 * Платежные системы
 *
 * @package concepture\yii2handbook\console\controllers
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class PaymentSystemController extends BaseController
{
    /** @var bool */
    public $localized = true;

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
                        'status-change',
                        PositionSortIndexAction::actionName(),
                        SortAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        AccessEnum::ADMIN
                    ],
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);

        return array_merge($actions,[
            'status-change' => [
                'class' => StatusChangeAction::class,
                'redirect' => false
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'redirect' => false
            ],
            PositionSortIndexAction::actionName() => [
                'class' => PositionSortIndexAction::class,
                'entityColumns' => [
                    'id',
                    'name',
                ],
                'labelColumn' => 'name',
            ],
            SortAction::actionName() => [
                'class' => SortAction::class,
                'serviceClass' => EntityTypePositionSortService::class
            ],
        ]);
    }
}