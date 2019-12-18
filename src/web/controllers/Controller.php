<?php

namespace concepture\yii2handbook\web\controllers;

use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller as Base;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;

/**
 * Базовый контроллер для модуля пользователя
 *
 * Class Controller
 * @package concepture\yii2user\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Controller extends Base
{
    use ControllerTrait;

    /**
     * @inheritDoc
     */
    protected function getAccessRules()
    {
        return [
            [
                'actions' => ['index', 'create','update', 'view','delete'],
                'allow' => true,
                'roles' => [UserRoleEnum::ADMIN],
            ]
        ];
    }
}
