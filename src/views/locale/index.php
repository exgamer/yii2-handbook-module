<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\enum\StatusEnum;

$this->setTitle(Yii::t('yii2admin', 'Пользователи'));
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
                        '<i class="icon-checkmark4"></i>'. Yii::t('yii2admin', 'Активировать'),
                        ['status-change', 'id' => $model['id'], 'status' => StatusEnum::ACTIVE],
                        [
                            'class' => 'dropdown-item',
                            'aria-label' => Yii::t('yii2admin', 'Активировать'),
                            'title' => Yii::t('yii2admin', 'Активировать'),
                            'data-confirm' => Yii::t('yii2admin', 'Активировать ?'),
                            'data-method' => 'post',
                        ]
                    );
                },
                'deactivate'=> function ($url, $model) {
                    if ($model['status'] == StatusEnum::INACTIVE){
                        return '';
                    }

                    return Html::a(
                        '<i class="icon-cross2"></i>'. Yii::t('yii2admin', 'Деактивировать'),
                        ['status-change', 'id' => $model['id'], 'status' => StatusEnum::INACTIVE],
                        [
                            'class' => 'dropdown-item',
                            'aria-label' => Yii::t('yii2admin', 'Деактивировать'),
                            'title' => Yii::t('yii2admin', 'Деактивировать'),
                            'data-confirm' => Yii::t('yii2admin', 'Деактивировать ?'),
                            'data-method' => 'post',
                        ]
                    );
                }
            ]
        ],
    ],
]); ?>

<?php Pjax::end(); ?>
