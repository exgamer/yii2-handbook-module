<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use concepture\yii2handbook\enum\SeoBlockPositionEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use kamaelkz\yii2admin\v1\widgets\lists\grid\EditableColumn;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->setTitle($searchModel::label());
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader();
?>


    <?php Pjax::begin(); ?>


<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'searchVisible' => true,
    'dragAndDrop' => true,
    'searchParams' => [
        'model' => $searchModel
    ],
    'columns' => [
            'id',
            'caption',
            [
                'attribute' => 'position',
                'value' => function($data){
                    return SeoBlockPositionEnum::labels()[$data->position] ?? null;
                },
            ],
            [
                'attribute' => 'sort',
                'class' => EditableColumn::class,
                'contentOptions' => [
                    'style' => 'width:15%',
                    'class'=> 'text-center'
                ]
            ],
            [
                'attribute'=>'status',
                'value' => function($data) {
                    return StatusEnum::labels()[$data->status] ?? null;
                }
            ],
            [
                'attribute'=>'is_deleted',
                'filter'=> IsDeletedEnum::arrayList(),
                'value' => function($data) {
                    return $data->isDeletedLabel();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {view} {delete} {undelete} {activate} {deactivate}',
                'buttons' => [
                    'activate'=> function ($url, $model) {
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
                ]
            ],
        ],
    ]); ?>


    <?php Pjax::end(); ?>
