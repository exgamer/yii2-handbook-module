<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

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
                'attribute' => 'url',
                'value' => function ($model) {
                    if(! $model->url) {
                        return '-';
                    }

                    return $model->url;
                }
            ],
            'caption',
            [
                'class'=>'yii\grid\ActionColumn',
                'dropdown' => false,
                'template'=>'{update}',
                'buttons'=>[
                    'update'=> function ($url, $model) {
                        return Html::a(
                            '<i class="icon-pencil6"></i>',
                            null,
                            [
                                'data-url' => Url::to(['update', 'id' => $model['id']]),
                                'class' => 'list-icons-item magic-modal-control',
                                'aria-label' => Yii::t('yii2admin', 'Редактирование'),
                                'title' => Yii::t('yii2admin', 'Редактирование'),
                                'data-pjax' => '0',
                                'data-modal-size' => 'modal-lg',
                                'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>