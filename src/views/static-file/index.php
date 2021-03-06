<?php

use concepture\yii2handbook\enum\StaticFileTypeEnum;
use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use yii\helpers\Url;

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
            'filename',
            'extension',
            [
                'attribute'=>'type',
                'filter'=> StaticFileTypeEnum::arrayList(),
                'value'=>function($data) {
                    return $data->typeLabel();
                }
            ],
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
                'value' => function($data) {
                    return $data->isDeletedLabel();
                }
            ],
            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{update} {activate} {deactivate} {delete}',
                'buttons'=>[
                    'update'=> function ($url, $model) {
                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }

                        return Html::a(
                            '<i class="icon-pencil6"></i>'. Yii::t('yii2admin', 'Редактировать'),
                            ['update', 'id' => $model['id']],
                            [
                                'class' => 'dropdown-item',
                                'title' => Yii::t('yii2admin', 'Редактировать'),
                                'data-pjax' => '0'
                            ]
                        );
                    },
                    'activate'=> function ($url, $model) {
                        if ($model['type'] == StaticFileTypeEnum::ROBOTS){
                            return '';
                        }
                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }

                        if ($model['status'] == StatusEnum::ACTIVE){
                            return '';
                        }

                        return Html::a(
                            '<i class="icon-checkmark4"></i>'. Yii::t('yii2admin', 'Активировать'),
                            ['status-change', 'id' => $model['id'], 'status' => StatusEnum::ACTIVE],
                            [
                                'class' => 'admin-action dropdown-item',
                                'data-pjax-id' => 'list-pjax',
                                'data-pjax-url' => Url::current([], true),
                                'data-swal' => Yii::t('yii2admin' , 'Активировать'),
                            ]
                        );
                    },
                    'deactivate'=> function ($url, $model) {
                        if ($model['type'] == StaticFileTypeEnum::ROBOTS){
                            return '';
                        }
                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }
                        if ($model['status'] == StatusEnum::INACTIVE){
                            return '';
                        }
                        return Html::a(
                            '<i class="icon-cross2"></i>'. Yii::t('yii2admin', 'Деактивировать'),
                            ['status-change', 'id' => $model['id'], 'status' => StatusEnum::INACTIVE],
                            [
                                'class' => 'admin-action dropdown-item',
                                'data-pjax-id' => 'list-pjax',
                                'data-pjax-url' => Url::current([], true),
                                'data-swal' => Yii::t('yii2admin' , 'Деактивировать'),
                            ]
                        );
                    },
                    'delete'=> function ($url, $model) {
                        if ($model['type'] == StaticFileTypeEnum::ROBOTS){
                            return '';
                        }

                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }

                        return Html::a(
                            '<i class="icon-trash"></i>'. Yii::t('yii2admin', 'Удалить'),
                            ['delete', 'id' => $model['id']],
                            [
                                'class' => 'admin-action dropdown-item',
                                'data-pjax-id' => 'list-pjax',
                                'data-pjax-url' => Url::current([], true),
                                'data-swal' => Yii::t('yii2admin' , 'Удалить'),
                            ]
                        );
                    }
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
