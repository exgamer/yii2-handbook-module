<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2cdnuploader\enum\StrategiesEnum;
use kamaelkz\yii2cdnuploader\widgets\CdnUploader;
$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();

?>

<?php Pjax::begin(['formSelector' => '#currency-form']); ?>

<?php $form = ActiveForm::begin(['id' => 'currency-form']); ?>
<?= Html::activeHiddenInput($model, 'locale'); ?>
<?php if (Yii::$app->localeService->catalogCount() > 1): ?>
    <ul class="nav nav-tabs nav-tabs-solid nav-justified bg-light">
        <?php foreach (Yii::$app->localeService->getByDomainMap() as $key => $locale):?>
            <li class="nav-item">
                <?= Html::a(
                    Yii::$app->localeService->catalogValue($locale, 'locale', 'caption'),
                    \yii\helpers\Url::current(['locale' => $key]),
                    ['class' => 'nav-link ' . ($key ==  $model->locale   ? "active" : "")]
                ) ?>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>
    <div class="card">
        <div class="card-body text-right">
            <?=  $saveRedirectButton?>
            <?=  $saveButton?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'symbol_native')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>
                </div>
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
            <?=  $saveRedirectButton?>
            <?=  $saveButton?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
