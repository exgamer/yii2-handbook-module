<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use concepture\yii2user\enum\UserRoleEnum;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2handbook\search\DynamicElementsSearch;
use concepture\yii2handbook\forms\DynamicElementsMultipleForm;
use concepture\yii2handbook\services\DomainService;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use concepture\yii2handbook\services\DynamicElementsService;
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditRollbackAction;
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditDynamicElementsAction;

/**
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DynamicElementsController extends Controller
{
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
                        'interactive-mode',
                        'update-multiple',
                        AuditDynamicElementsAction::actionName(),
                        AuditRollbackAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        UserRoleEnum::ADMIN
                    ],
                ],
                [
                    'actions' => [
                        AuditDynamicElementsAction::actionName(),
                        AuditRollbackAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        UserRoleEnum::SUPER_ADMIN
                    ],
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'interactive-mode' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        if(in_array($action->id, ['interactive-mode'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * @return DynamicElementsService
     */
    private function getDynamicElementsService()
    {
        return Yii::$app->dynamicElementsService;
    }

    /**
     * @return DomainService
     */
    private function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['view']);

        return array_merge($actions,[
            AuditDynamicElementsAction::actionName() => AuditDynamicElementsAction::class,
            AuditRollbackAction::actionName() => AuditRollbackAction::class,
        ]);
    }

    /**
     * Список
     *
     * @return string HTML
     */
    public function actionIndex()
    {
        $searchModel = Yii::createObject(DynamicElementsSearch::class);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getDynamicElementsService()->getDataProvider([], [], $searchModel);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Обновление одно записи
     *
     * @param integer $id
     * @param null|string $domainAlias
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionUpdate($id, $domainAlias = null)
    {
        if($domainAlias && $domainAlias !== $this->getDomainService()->getCookie()) {
            $this->getDomainService()->setCookie($domainAlias);

            return $this->redirect(['update', 'id' => $id], 301);
        }

        $service = $this->getService();
        $originModel = $service->findById($id);
        if (! $originModel){
            throw new NotFoundHttpException();
        }

        $model = $service->getRelatedForm();
        $model->setAttributes($originModel->attributes, false);
        if (method_exists($model, 'customizeForm')) {
            $model->customizeForm($originModel);
        }

        if ($model->load(Yii::$app->request->post())) {
            $originModel->setAttributes($model->attributes);
            if ($model->validate(null, true, $originModel)) {
                if (($result = $service->update($model, $originModel)) != false) {

                    if(Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                        return $this->redirect(['index']);
                    }
                }
            }

            $model->addErrors($originModel->getErrors());
        }

        return $this->render('update', [
            'model' => $model,
            'originModel' => $originModel,
        ]);
    }

    /**
     * Редактирование пачкой со страницы
     *
     * @param string $ids
     * @param string $domainAlias
     */
    public function actionUpdateMultiple($ids, $domainAlias = null)
    {
        if($domainAlias && $domainAlias !== $this->getDomainService()->getCookie()) {
            $this->getDomainService()->setCookie($domainAlias);

            return $this->redirect(['update', 'hash' => $hash], 301);
        }

        $form = new DynamicElementsMultipleForm();
        $ids = explode(',', $ids);
        if(! $ids || ! is_array($ids)) {
            throw new BadRequestHttpException('Bad Request');
        }

        $items = $this->getDynamicElementsService()->getAllByCondition(function(ActiveQuery $query) use($ids) {
            $query->andWhere(['id' => $ids]);
            $query->orderBy('is_general', 'id');
        });
        foreach ($items as $item) {
            $form->setVirtualAttribute($item->name, $item->value);
            $form->setStringValidator($item->name, $item->caption);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->getDynamicElementsService()->updateMultiple($form);

            if(Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update-multiple', [
            'items' => $items,
            'model' => $form,
        ]);
    }

    /**
     * Интерактивный мод
     */
    public function actionInteractiveMode()
    {
        if(null === Yii::$app->request->post('value')) {
            return null;
        }

        return $this->getDynamicElementsService()->setInteractiveMode(Yii::$app->request->post('value'));
    }
}
