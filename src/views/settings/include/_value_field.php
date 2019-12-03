<?php
    use concepture\yii2handbook\enum\SettingsTypeEnum;
?>
<?php if ($model->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
<?php endif;?>
