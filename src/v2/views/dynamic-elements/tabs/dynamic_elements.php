<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use \concepture\yii2logic\helpers\UrlHelper;
use concepture\yii2logic\enum\AccessEnum;

$is_superadmin = Yii::$app->getUser()->can(AccessEnum::SUPERADMIN);

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'searchVisible' => false,
    'searchParams' => [
        'model' => $searchModel
    ],
    'columns' => [
        [
            'attribute' => 'caption',
            'value' => function ($model) {
                $url = Url::to(['/handbook/dynamic-elements/update', 'id' => $model->id, 'domain_id' => $model->domain_id]);

                $caption = Yii::t('de', $model->caption);
                return <<<HTML
                        <span class="editable-column magic-modal-control" data-url="$url" data-modal-size="modal-full" data-callback="function(){callbackHelper.reloadPjax('#list-pjax')}">
                        {$caption}
</span>
HTML;
            },
            'format' => 'raw'
    ],
        [
            'attribute' => 'value',
            'value' => function($model) {
                if($model->value === '' OR $model->value === null) {
                    return null;
                }

                if($model->type === \concepture\yii2handbook\v2\enum\DynamicElementsTypeEnum::TEXT_EDITOR) {
                    return htmlspecialchars_decode(strip_tags(mb_substr($model->value, 0, 150))) . ' ...';
                }

                if($model->type === \concepture\yii2handbook\v2\enum\DynamicElementsTypeEnum::CHECKBOX) {
                    return Yii::$app->formatter->asBoolean($model->value);
                }

                return $model->value;
            },
            'headerOptions' => [
                'style' => 'width:60%',
            ],
            'format' => 'raw'
        ],
        [
            'attribute' => 'general',
            'format' => 'boolean'
        ]
    ],
]); ?>