<?php
    use concepture\yii2handbook\enum\SettingsTypeEnum;
?>
<?php if ($model->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form->field($model, 'value')->textarea(); ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('@concepture/yii2handbook/views/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => 'value',
        'originModel' => isset($originModel) ? $originModel : null
    ]) ?>
<?php endif;?>
<?php if ($model->type == SettingsTypeEnum::CHECKBOX) : ?>
    <?= $form
        ->field($model, 'value', [
            'template' => '
            <div class="form-check form-check-inline mt-2">
                {input}
            </div>
            {error}
                                        '
        ])
        ->checkbox(
            [
                'class' => 'form-check-input-styled-primary',
                'labelOptions' => [
                    'class' => 'form-check-label control-label',
                ],
                'label' => $model->getAttributeLabel('value')
            ],
            true
        );
    ?>
<?php endif;?>
