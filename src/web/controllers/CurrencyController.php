<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\actions\web\StatusChangeAction;

/**
 * Class CurrencyController
 *
 * @package concepture\yii2handbook\web\controllers
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class CurrencyController extends Controller
{
    public $localized = true;

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
