<?php

namespace concepture\yii2handbook\web\controllers;

use yii\helpers\ArrayHelper;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\Controller as Base;
use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;

use kamaelkz\yii2admin\v1\modules\audit\actions\AuditAction;
use kamaelkz\yii2admin\v1\modules\audit\services\AuditService;
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditRollbackAction;

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
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (
            $action->id == 'update'
            && method_exists($action->controller, 'getService')
            && AuditService::isAuditAllowed($modelClass = $action->controller->getService()->getRelatedModelClass())
        ) {
            $this->getView()->viewHelper()->pushPageHeader(
                [AuditAction::actionName(), 'id' => \Yii::$app->request->get('id')],
                \Yii::t('yii2handbook', 'Аудит'),
                'icon-eye'
            );
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritDoc
     */
    protected function getAccessRules()
    {
        return ArrayHelper::merge(
            parent::getAccessRules(),
            [
                [
                    'actions' => ['index', 'create','update', 'view','delete'],
                    'allow' => true,
                    'roles' => [UserRoleEnum::ADMIN],
                ],
                [
                    'actions' => [
                        AuditAction::actionName(),
                        AuditRollbackAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        UserRoleEnum::SUPER_ADMIN,
                    ],
                ],
            ],
        );
    }

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        return ArrayHelper::merge($actions,[
            AuditAction::actionName() => AuditAction::class,
            AuditRollbackAction::actionName() => AuditRollbackAction::class,
        ]);
    }
}
