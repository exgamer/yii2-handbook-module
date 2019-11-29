<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use concepture\yii2logic\enum\StatusEnum;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Языки');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('handbook', 'Добавить язык'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'locale',
            'caption',
            'sort',
            [
                'attribute'=>'status',
                'filter'=> StatusEnum::arrayList(),
                'value'=>function($data) {
                    return $data->statusLabel();
                }
            ],
            'created_at',
            'updated_at',

            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{view} {update} {activate} {deactivate}',
                'buttons'=>[
                    'activate'=> function ($url, $model) {

                        if ($model['status'] == StatusEnum::ACTIVE){
                            return '';
                        }

                        return Html::a(
                            '<span class="glyphicon glyphicon-ok"></span>',
                            ['status-change', 'id' => $model['id'], 'status' => StatusEnum::ACTIVE],
                            [
                                'title' => Yii::t('handbook', 'Активировать'),
                                'data-confirm' => Yii::t('handbook', 'Активировать ?'),
                                'data-method' => 'post',
                            ]
                        );
                    },
                    'deactivate'=> function ($url, $model) {

                        if ($model['status'] == StatusEnum::INACTIVE){
                            return '';
                        }

                        return Html::a(
                            '<span class="glyphicon glyphicon-remove"></span>',
                            ['status-change', 'id' => $model['id'], 'status' => StatusEnum::INACTIVE],
                            [
                                'title' => Yii::t('handbook', 'Деактивировать'),
                                'data-confirm' => Yii::t('handbook', 'Деактивировать ?'),
                                'data-method' => 'post',
                            ]
                        );
                    }
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
