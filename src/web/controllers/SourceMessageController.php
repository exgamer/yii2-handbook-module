<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use concepture\yii2logic\db\ActiveQuery;
use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2handbook\traits\ServicesTrait;
use concepture\yii2handbook\services\MessageService;
use kamaelkz\yii2admin\v1\controllers\BaseController;
use concepture\yii2handbook\forms\MessageMultipleForm;
use concepture\yii2handbook\search\SourceMessageSearch;
use concepture\yii2logic\db\ActiveQuery as CoreActiveQuery;

/**
 * Контроллер оригиналов переводов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SourceMessageController extends BaseController
{
    use ServicesTrait;

    /**
     * @return MessageService
     */
    private function getMessageService()
    {
        return Yii::$app->messageService;
    }

    /**
     * @return array
     */
    protected function getAccessRules()
    {
        return [
            [
                'actions' => [
                    'index',
                    'update'
                ],
                'allow' => true,
                'roles' => [
                    UserRoleEnum::ADMIN
                ],
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $parent = parent::actions();
        unset($parent['index'], $parent['create'], $parent['update'], $parent['delete']);

        return $parent;
    }

    /**
     * Список элементов для перевода (оригиналы)
     *
     * @return string HTML
     */
    public function actionIndex()
    {
        $searchClass = SourceMessageSearch::class;
        $searchModel = Yii::createObject($searchClass);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getService()->getDataProvider([], [], $searchModel);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Редактирование пачкой
     *
     * @param integer $id
     */
    public function actionUpdate($id)
    {
        $form = new MessageMultipleForm();
        $items = $this->getMessageService()->getAllByCondition(function(ActiveQuery $query) use($id) {
            $query->select([
                "*",
                new Expression("CASE WHEN language ='ru' THEN 1 ELSE -1 END as priority")
            ]);
            $query->andWhere(['id' => (int) $id]);
            $query->orderBy(['priority' => SORT_DESC,'id' => SORT_DESC]);
            $query->indexBy('language');
        });
        foreach ($items as $item) {
            $form->setVirtualAttribute($item->language, $item->translation);
            $form->setStringValidator($item->language, $item->translation);
        }

        $sourceMessage = null;
        $item = reset($items);
        if($item) {
            $sourceMessage = $item->sourceMessage;
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->getMessageService()->updateMultiple($form);

            return $this->responseNotify();
        }

        $countries = $this->countryService()->getAllByCondition(function(ActiveQuery $query) {
            $query->indexBy('iso');
        });

        $itemsByLanguage = [];
        $domainMap = \Yii::$app->domainService->getDomainMap();
        $countryLanguage = ArrayHelper::map($domainMap, 'country', 'country', 'language');

        foreach ($countryLanguage as $lang => $item) {
            foreach ($item as $iso) {
                $itemsByLanguage[$lang][] = $items[$iso];
            }
        }

        $config = ['pagination' => false];
        $langs = array_keys($countryLanguage);
        $condition = function(CoreActiveQuery $query) use ($langs) {
            $query->andFilterWhere(['in', 'locale', $langs]);
            $query->indexBy('locale');
        };
        $search = $this->localeService()->getRelatedSearchModel();
        $languages = $this->localeService()
            ->getDataProvider([], $config, $search, '', $condition)
            ->getModels();

        return $this->render('update', [
            'items' => $items,
            'model' => $form,
            'sourceMessage' => $sourceMessage,
            'countries' => $countries,
            'itemsByLanguage' => $itemsByLanguage,
            'languages' => $languages,
        ]);
    }
}