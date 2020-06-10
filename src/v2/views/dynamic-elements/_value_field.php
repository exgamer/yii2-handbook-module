<?php
use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
?>
<?php if ($originModel->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form->field($model, $attribute)->textInput(['maxlength' => true])
        ->label($label ?? $model->getAttributeLabel('values'));
    ?>
<?php endif;?>

<?php if ($originModel->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form->field($model, $attribute)->textarea()
        ->label($label ?? $model->getAttributeLabel('values'));
    ?>
<?php endif;?>

<?php if ($originModel->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('@concepture/yii2handbook/views/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => $attribute,
        'originModel' => isset($originModel) ? $originModel : null,
        'label' => $label ?? $model->getAttributeLabel('values')
    ]) ?>
<?php endif;?>
<?php if ($originModel->type == SettingsTypeEnum::CHECKBOX) : ?>
    <?= $form
        ->field($model, $attribute, [
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
                'label' => $label ?? $model->getAttributeLabel('values')
            ],
            true
        );
    ?>
<?php endif;?>