<?php

use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use yii\helpers\Url;

$this->setTitle(Yii::t('yii2admin', 'Просмотр'));
?>

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form', 'enablePushState' => false]); ?>

<div class="d-md-flex align-items-md-start">
    <?= $this->render('@concepture/yii2handbook/views/include/_domains_sidebar', [
        'domain_id' => $domain_id,
        'model' => $model ?? null,
        'url' => ['update', 'id' => $model->id]
    ]);
    ?>
    <div class="w-100">
        <div class="card">
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'caption',
                        [
                            'attribute' => 'value',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return $data->value;
                            },
                        ],
                    ],
                ]) ?>
            </div>
        </div>

    </div>
</div>
<?php Pjax::end(); ?>

