<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model concepture\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'type')->dropDownList(
        \concepture\yii2handbook\enum\TagTypeEnum::arrayList(),
        []
    );?>
    <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'domain_id')->dropDownList(
        Yii::$app->domainService->catalog(),
        [
            'prompt' => Yii::t('backend', 'Выберите домен')
        ]
    );?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
