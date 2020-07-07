<?php

namespace concepture\yii2handbook\v2\web\controllers;

use concepture\yii2logic\enum\PermissionEnum;
use concepture\yii2logic\helpers\AccessHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2handbook\v2\search\DynamicElementsSearch;
use concepture\yii2handbook\v2\forms\DynamicElementsMultipleForm;
# todo: разобраться
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditRollbackAction;
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditDynamicElementsAction;
use concepture\yii2logic\enum\AccessEnum;
use concepture\yii2handbook\web\controllers\Controller;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;

/**
 * Динамические элементы
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsController extends Controller
{
    use HandbookServicesTrait;

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
                        'interactive-mode',
                        'update-multiple',
                        'manage'
                    ],
                    'allow' => true,
                    'roles' => [
                        AccessEnum::ADMIN,
                        AccessHelper::getAccessPermission($this, PermissionEnum::EDITOR),
                        AccessHelper::getDomainAccessPermission($this, PermissionEnum::EDITOR)
                    ],
                ],
                [
                    'actions' => [
                        AuditDynamicElementsAction::actionName(),
                        AuditRollbackAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        AccessEnum::SUPERADMIN,
                    ],
                ],
                [
                    'actions' => [
                        AuditDynamicElementsAction::actionName(),
                        AuditRollbackAction::actionName(),
                    ],
                    'allow' => true,
                    'roles' => [
                        AccessEnum::SUPERADMIN,
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
        # переопределение представлений
        $this->module->setViewPath('@concepture/yii2handbook/v2/views');

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
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
        $dataProvider =  $this->dynamicElementsService()->getDataProvider([], [], $searchModel);
        $this->storeUrl();

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
    public function actionUpdate($id, $domain_id)
    {
        $service = $this->getService();
        $model = $service->getOneByCondition(function(ActiveQuery $query) use($id, $domain_id) {
            $query->andWhere(['id' => $id]);
            $query->applyPropertyUniqueValue($domain_id);
        });
        if (! $model){
            throw new NotFoundHttpException();
        }

        $form = $service->getRelatedForm();
        $form->setAttributes($model->attributes, false);
        if (method_exists($form, 'customizeForm')) {
            $form->customizeForm($model);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (($result = $service->update($form, $model)) != false) {
                if ( RequestHelper::isMagicModal()){
                    return $this->responseNotify();
                }

                if(Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {

                    $redirectStore = $this->redirectStoreUrl();
                    if($redirectStore) {
                        return $redirectStore;
                    }

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('update', [
            'model' => $form,
            'originModel' => $model,
            'domain_id' => $domain_id,
        ]);
    }

    /**
     * Редактирование пачкой со страницы
     *
     * @param string $ids
     * @param string $domainAlias
     */
    public function actionUpdateMultiple($ids, $domain_id)
    {
        $form = new DynamicElementsMultipleForm();
        $stringIds = $ids;
        $ids = explode(',', $ids);
        if(! $ids || ! is_array($ids)) {
            throw new BadRequestHttpException('Bad Request');
        }

        $items = $this->dynamicElementsService()->getAllByCondition(function(ActiveQuery $query) use($ids, $domain_id) {
            $query->andWhere(['id' => $ids]);
            $query->applyPropertyUniqueValue($domain_id);
            $query->orderBy('general', 'id');
        });
        foreach ($items as $item) {
            $form->setVirtualAttribute($item->name, $item->value);
            $form->setStringValidator($item->name, $item->caption);
        }

        $form->domain_id = $domain_id;
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->dynamicElementsService()->updateMultiple($form);

            if(Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                $redirectStore = $this->redirectStoreUrl();
                if($redirectStore) {
                    return $redirectStore;
                }

                return $this->redirect(['index']);
            }
        }

        $domainsData = $this->domainService()->getDomainsData();

        return $this->render('update-multiple', [
            'items' => $items,
            'model' => $form,
            'domain_id' => $domain_id,
            'ids' => $stringIds,
            'domainsData' => $domainsData
        ]);
    }

    /**
     * Управление элементами
     *
     * @param integer $domain_id
     * @param string $dynamic_elements_ids
     * @param null|string $translation_ids
     * @param string $tab
     * @return string|\yii\web\Response|null
     * @throws BadRequestHttpException
     */
    public function actionManage($domain_id, $dynamic_elements_ids, $translation_ids = null, $manage_tab = 'de')
    {
        return $this->dynamicElementsService()->renderManageTables($domain_id, $dynamic_elements_ids, $translation_ids, $manage_tab);
    }

    /**
     * Интерактивный мод
     */
    public function actionInteractiveMode()
    {
        if(null === Yii::$app->request->post('value')) {
            return null;
        }

        return $this->dynamicElementsService()->setInteractiveMode(Yii::$app->request->post('value'));
    }
}
