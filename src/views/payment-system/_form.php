<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
use kamaelkz\yii2cdnuploader\enum\StrategiesEnum;
use kamaelkz\yii2cdnuploader\widgets\CdnUploader;
use kamaelkz\yii2admin\v1\widgets\ {
    formelements\pickers\DatePicker,
    formelements\pickers\TimePicker
};

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();
?>

<?php Pjax::begin(['formSelector' => '#services-form']); ?>
<?php if (Yii::$app->localeService->catalogCount() > 1): ?>
    <ul class="nav nav-tabs nav-tabs-solid nav-justified bg-light">
        <?php foreach (Yii::$app->localeService->getByDomainMap() as $key => $locale):?>
            <li class="nav-item">
                <?= Html::a(
                    $locale,
                    \yii\helpers\Url::current(['locale' => $key]),
                    ['class' => 'nav-link ' . ($key ==  $model->locale   ? "active" : "")]
                ) ?>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>
<?php
$form = ActiveForm::begin(['id' => 'services-form', 'model' => $originModel ?? new \common\models\Features()]);

echo Html::activeHiddenInput($model, 'locale');
?>
<div class="card">
    <div class="card-body text-right">
        <?= $saveRedirectButton?>
        <?= $saveButton?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <legend class="font-weight-semibold text-uppercase font-size-sm">
            <?= Yii::t('yii2admin', 'Основные данные') ;?>
        </legend>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <?= $form
                    ->field($model, 'logo')
                    ->widget(CdnUploader::class, [
                        'model' => $model,
                        'attribute' => 'logo',
                        'strategy' => StrategiesEnum::TRUSTED,
                        'resizeBigger' => false,
                        'width' => 50,
                        'height' => 23,
                        'options' => [
                            'plugin-options' => [
                                'maxFileSize' => 2000000,
                            ]
                        ]
                    ])
                    ->error(false)
                    ->hint(false);
                ?>
            </div>
        </div>
        <legend class="font-weight-semibold text-uppercase font-size-sm">
            <?= Yii::t('yii2admin', 'Дополнительно') ;?>
        </legend>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form
                    ->field($model, 'status', [
                        'template' => '
                                                <div class="form-check form-check-inline mt-2">
                                                    {input}
                                                </div>
                                                {error}
                                            '
                    ])
                    ->checkbox(
                        [
                            'label' => Yii::t('yii2admin', 'Активировано'),
                            'class' => 'form-check-input-styled-primary',
                            'labelOptions' => ['class' => 'form-check-label control-label']
                        ],
                        true
                    )
                ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body text-right">
        <?= $saveRedirectButton?>
        <?= $saveButton?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
