<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use \concepture\yii2logic\helpers\UrlHelper;
use concepture\yii2logic\enum\AccessEnum;

$is_superadmin = Yii::$app->getUser()->can(AccessEnum::SUPERADMIN);

$this->setTitle($searchModel::label());
$this->pushBreadcrumbs($this->title);
//$this->viewHelper()->pushPageHeader(null, null, null,
//    [
//        'class' => 'magic-modal-control',
//        'data-url' => Url::to(['create']),
//        'data-modal-size' => 'modal-lg',
//        'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
//    ]
//);
?>
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'searchVisible' => true,
        'searchParams' => [
            'model' => $searchModel
        ],
        'columns' => [
            'id',
            [
                'attribute' => 'name',
                'visible' => $is_superadmin
            ],
            [
                'attribute' => 'route',
                'label' => Yii::t('handbook', 'Адрес'),
                'value' => function ($model) {
                    if($model->general) {
                        return "-";
                    }

                    return UrlHelper::getFrontendUrlManager()->createUrl(["/{$model->route}", Json::decode($model->route_params)]);
                },
            ],
            [
                'attribute' => 'caption',
                'value' => function($model) {
                    return Yii::t('de', $model->caption);
                }
            ],
            [
                'class'=>'yii\grid\ActionColumn',
                'dropdown' => false,
                'template'=>'{update}',
                'buttons'=>[
                    'update'=> function ($url, $model) {
                        return Html::a(
                            '<i class="icon-pencil6"></i>',
                            ['update', 'id' => $model['id'], 'domain_id' => $model['domain_id']],
                            [
                                'class' => 'list-icons-item',
                                'title' => Yii::t('yii2admin', 'Редактирование'),
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>