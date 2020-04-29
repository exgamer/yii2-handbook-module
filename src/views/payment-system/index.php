<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

$this->setTitle($searchModel::label());
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader();
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
            'attribute' => 'logo',
            'value' => function($model) {
                if(! $model->logo) {
                    return null;
                }

                return Html::img(
                    Yii::$app->cdnService->path($model->getImageAttribute('logo')),
                    [
                        'style' => 'height: 65px'
                    ]
                );
            },
            'format' => 'raw',
            'headerOptions' => [
                'style' => 'width:10%',
                'class' => 'text-center'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        'name',
        [
            'attribute'=>'status',
            'filter'=> StatusEnum::arrayList(),
            'value'=>function($data) {
                return $data->statusLabel();
            }
        ],
        'created_at',
        [
            'attribute'=>'is_deleted',
            'filter'=> IsDeletedEnum::arrayList(),
            'value'=>function($data) {
                return $data->isDeletedLabel();
            }
        ],
        [
            'class'=>'yii\grid\ActionColumn',
            'template'=>'{update} <div class="dropdown-divider"></div> {activate} {deactivate} {delete}',
            'buttons'=>[
                'update'=> function ($url, $model) {
                    if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                        return '';
                    }

                    return Html::a(
                        '<i class="icon-pencil6"></i>'. Yii::t('yii2admin', 'Редактировать'),
                        ['update', 'id' => $model['id'], 'locale' => $model['locale']],
                        [
                            'class' => 'dropdown-item',
                            'aria-label' => Yii::t('yii2admin', 'Редактировать'),
                            'title' => Yii::t('yii2admin', 'Редактировать'),
                            'data-pjax' => '0'
                        ]
                    );
                },
                'activate'=> function ($url, $model) {
                    if ($model['is_deleted'] == IsDeletedEnum::DELETED || $model['status'] == StatusEnum::ACTIVE){
                        return '';
                    }

                    return Html::a(
                        '<i class="icon-checkmark4"></i>'. Yii::t('yii2admin', 'Активировать'),
                        ['status-change', 'id' => $model['id'], 'status' => StatusEnum::ACTIVE, 'locale' => $model['locale']],
                        [
                            'class' => 'admin-action dropdown-item',
                            'data-pjax-id' => 'list-pjax',
                            'data-pjax-url' => Url::current([], true),
                            'data-swal' => Yii::t('yii2admin' , 'Активировать'),
//                                'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
                        ]
                    );
                },
                'deactivate'=> function ($url, $model) {
                    if ($model['is_deleted'] == IsDeletedEnum::DELETED || $model['status'] == StatusEnum::INACTIVE){
                        return null;
                    }

                    return Html::a(
                        '<i class="icon-cross2"></i>'. Yii::t('yii2admin', 'Деактивировать'),
                        ['status-change', 'id' => $model['id'], 'status' => StatusEnum::INACTIVE, 'locale' => $model['locale']],
                        [
                            'class' => 'admin-action dropdown-item',
                            'data-pjax-id' => 'list-pjax',
                            'data-pjax-url' => Url::current([], true),
                            'data-swal' => Yii::t('yii2admin' , 'Деактивировать'),
//                                'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
                        ]
                    );
                },
                'delete'=> function ($url, $model) {
                    if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                        return '';
                    }

                    return Html::a(
                        '<i class="icon-trash"></i>'. Yii::t('yii2admin', 'Удалить'),
                        ['delete', 'id' => $model['id'], 'locale' => $model['locale']],
                        [
                            'class' => 'admin-action dropdown-item',
                            'data-pjax-id' => 'list-pjax',
                            'data-pjax-url' => Url::current([], true),
                            'data-swal' => Yii::t('yii2admin' , 'Удалить'),
//                                'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
                        ]
                    );
                }
            ]
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
