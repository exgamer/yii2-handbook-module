<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
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
        'caption',
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
            'template'=>'{view} {update} {delete}',
            'buttons'=>[
                'update'=> function ($url, $model) {
                    if ($model['is_deleted'] == IsDeletedEnum::DELETED){
                        return '';
                    }

                    return Html::a(
                        '<i class="icon-pencil6"></i>'. Yii::t('yii2admin', 'Редактирование'),
                        ['update', 'id' => $model['id']],
                        [
                            'class' => 'dropdown-item',
                            'aria-label' => Yii::t('yii2admin', 'Редактирование'),
                            'title' => Yii::t('yii2admin', 'Редактирование'),
                            'data-pjax' => '0'
                        ]
                    );
                },
                'delete'=> function ($url, $model) {
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

