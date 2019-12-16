<?php
    use concepture\yii2handbook\enum\SettingsTypeEnum;
    use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
?>
<?php if ($model->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form->field($model, 'value')->textarea(); ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => 'value',
        'originModel' => $originModel
    ]) ?>
<?php endif;?>
