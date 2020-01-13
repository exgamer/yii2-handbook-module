<?php

namespace concepture\yii2handbook\web\controllers;

use Yii;
use yii\db\ActiveQuery;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2handbook\services\SeoSettingsService;
use concepture\yii2handbook\search\SeoSettingsSearch;
use concepture\yii2handbook\forms\SeoSettingsMultipleForm;

/**
 * Class SeoSettingsController
 * @package concepture\yii2handbook\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsController extends Controller
{
    /**
     * @return SeoSettingsService
     */
    protected function getSeoSettingsService()
    {
        return Yii::$app->seoSettingsService;
    }

    /**
     * @inheritDoc
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index'], $actions['update'], $actions['view']);

        return $actions;
    }

    /**
     * Список
     *
     * @return string HTML
     */
    public function actionIndex()
    {
        $searchModel = Yii::createObject(SeoSettingsSearch::class);
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider =  $this->getSeoSettingsService()->getDataProviderGroupByHash();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Редактирование пачкой
     *
     * @param string $hash
     */
    public function actionUpdate($hash)
    {
        $form = new SeoSettingsMultipleForm();
        $items = $this->getSeoSettingsService()->getAllByHash((string) $hash);
        foreach ($items as $item) {
            $form->setVirtualAttribute($item->name, $item->value);
            $form->setRequiredValidator($item->name, $item->caption);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->getSeoSettingsService()->updateMultiple($form);

            if(Yii::$app->request->post(RequestHelper::REDIRECT_BTN_PARAM)) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'items' => $items,
            'model' => $form,
        ]);
    }
}
