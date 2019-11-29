<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use concepture\yii2handbook\converters\LocaleConverter;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Сущности');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('user', 'Добавить сущность'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'table_name',
            'caption',
            'created_at',
            'updated_at',

            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{view} {update}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
