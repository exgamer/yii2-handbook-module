<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

$this->setTitle($searchModel::label());
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(null, null, null,
    [
        'class' => 'magic-modal-control',
        'data-url' => Url::to(['create']),
        'data-modal-size' => 'modal-lg',
        'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
    ]
);
?>
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'searchVisible' => true,
        'searchParams' => [
            'model' => $searchModel
        ],
        'columns' => [
            'url',
            [
                'attribute' => 'hash_count',
                'headerOptions' => [
                    'class' => 'text-center',
                    'style' => 'width : 25%'
                ],
                'contentOptions' => [
                    'class' => 'text-center',
                ],
                'value' => function($data) {
                    return Html::tag('span', $data->hash_count, ['class' => 'badge bg-primary']);
                },
                'format' => 'raw'
            ],
            [
                'class'=>'yii\grid\ActionColumn',
                'dropdown' => false,
                'template'=>'{update}',
                'buttons'=>[
                    'update'=> function ($url, $model) {
                        return Html::a(
                            '<i class="icon-pencil6"></i>',
                            ['update', 'hash' => $model['url_md5_hash']],
                            [
                                'class' => 'list-icons-item',
                                'title' => Yii::t('yii2admin', 'Редактирование'),
                                'data-pjax' => '0'
                            ]
                        );
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>