<?php

use yii\helpers\Url;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\lists\grid\GridView;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'searchVisible' => false,
    'searchParams' => [
        'model' => $searchModel
    ],
    'columns' => [
        [
            'attribute' => 'message',
            'contentOptions' => [
                'style' => 'width:30%'
            ],
            'value' => function ($model) {
                $url = Url::to(['/handbook/source-message/update', 'id' => $model->id]);

                return <<<HTML
                        <span class="editable-column magic-modal-control" data-url="$url" data-modal-size="modal-full" data-callback="function(){callbackHelper.reloadPjax('#list-pjax')}">
                        $model->message
</span>
HTML;
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'defaultTranslation',
            'contentOptions' => [
                'style' => 'width:30%'
            ],
        ],
        [
            'attribute' => 'messageState',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ],
            'value' => function ($model) {
                $color = 'success';
                if($model->fillCount > 0 && $model->fillCount < $model->allCount) {
                    $color = 'warning';
                }
                if($model->fillCount === 0) {
                    $color = 'danger';
                }

                return "<span class='badge badge-{$color}'>{$model->messageState}</span>";
            },
            'format' => 'raw'
        ],
    ],
]); ?>

