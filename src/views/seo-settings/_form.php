<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_h1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'seo_text')->widget(CKEditor::className(),[
        'editorOptions' => [
            'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
            'inline' => false, //по умолчанию false
            'allowedContent' => true,
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('user', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
