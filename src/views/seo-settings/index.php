<?php

use yii\helpers\Html;
use yii\grid\GridView;
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
        'url',
        'seo_h1',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'created_at',
        'updated_at',

        [
            'class'=>'yii\grid\ActionColumn',
            'template'=>'{view} {update} {delete}',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>