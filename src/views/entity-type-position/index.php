<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;

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
            'caption',
            'alias',
            [
                'attribute' => 'entity_type_id',
                'value' => function ($model) {
                    return $model->entityType->caption;
                }
            ],
            'max_count',
            'created_at',
            'updated_at',
            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{update}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
