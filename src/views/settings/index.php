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
        'id',
        'name',
        'value',
        'description',
        'created_at',
        'updated_at',

        [
            'class'=>'yii\grid\ActionColumn',
            'template'=>'{view} {update}',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
