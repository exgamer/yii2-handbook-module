<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use concepture\yii2handbook\enum\DeclinationFormatEnum;

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();

?>

<?php Pjax::begin(['formSelector' => '#locale-form']); ?>

<?php $form = ActiveForm::begin(['id' => 'locale-form']); ?>
<?= Html::activeHiddenInput($model, 'locale_id'); ?>
<?php if (Yii::$app->localeService->catalogCount() > 1): ?>
    <ul class="nav nav-tabs nav-tabs-solid nav-justified bg-light">
        <?php $locale_id = Yii::$app->localeService->getCurrentLocaleId(); ?>
        <?php foreach (Yii::$app->localeService->getByDomainMap() as $key => $locale):?>
            <li class="nav-item">
                <?= Html::a(
                    Yii::$app->localeService->catalogValue($locale, 'locale', 'caption', function (\concepture\yii2logic\db\ActiveQuery $query) use ($locale_id) {
                        $query->resetCondition();
                        $query->where(['p.locale_id' => $locale_id])->active();
                    }),
                    \yii\helpers\Url::current(['locale' => $key]),
                    ['class' => 'nav-link ' . ($key ==  $model->locale   ? "active" : "")]
                ) ?>
            </li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>
<div class="card">
    <div class="card-body text-right">
        <?= $saveRedirectButton?>
        <?= $saveButton?>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'locale')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'declination_format')->dropDownList(DeclinationFormatEnum::arrayList()) ?>
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
    <?php ActiveForm::end();?>
<?php Pjax::end(); ?>
