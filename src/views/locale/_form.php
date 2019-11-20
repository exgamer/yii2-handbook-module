<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'locale')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
