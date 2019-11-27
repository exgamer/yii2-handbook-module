<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use concepture\yii2logic\enum\IsDeletedEnum;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Теги');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('user', 'Добавить тег'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'caption',
            'description',
            [
                'attribute'=>'domain_id',
                'filter'=> Yii::$app->domainService->catalog(),
                'value'=>function($data) {
                    return $data->getDomainName();
                }
            ],
            [
                'attribute'=>'user_id',
                'filter'=> Yii::$app->userService->catalog(),
                'value'=>function($data) {
                    return $data->getUserName();
                }
            ],
            [
                'attribute'=>'type',
                'filter'=> \concepture\yii2handbook\enum\TagTypeEnum::arrayList(),
                'value'=>function($data) {
                    return $data->getTypeLabel();
                }
            ],
            'created_at',
            'updated_at',
            [
                'attribute'=>'is_deleted',
                'filter'=> IsDeletedEnum::arrayList(),
                'value'=>function($data) {
                    return $data->isDeletedLabel();
                }
            ],
            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{view} {update} {delete}' ,
                'buttons'=>[
                    'update'=> function ($url, $model) {
                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }

                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            ['update', 'id' => $model['id']],
                            ['data-pjax' => '0']
                        );
                    },
                    'delete'=> function ($url, $model) {
                        if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                            return '';
                        }

                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            ['delete', 'id' => $model['id']],
                            [
                                'title' => Yii::t('user', 'Удалить'),
                                'data-confirm' => Yii::t('handbook', 'Удалить ?'),
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
