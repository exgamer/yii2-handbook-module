<?php

use concepture\yii2handbook\enum\SettingsTypeEnum;
use concepture\yii2handbook\v2\models\DynamicElements;

$hint = null;
if(isset($originModel) && $originModel instanceof DynamicElements && $originModel->value_params) {
    $hint = Yii::$app->dynamicElementsService->getValueParamsHint($originModel);
}

?>
<?php if ($originModel->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form
        ->field($model, $attribute)->textInput(['maxlength' => true])
        ->label($label ?? $model->getAttributeLabel('values'))
        ->hint($hint);
    ?>
<?php endif;?>

<?php if ($originModel->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form
        ->field($model, $attribute)->textarea()
        ->label($label ?? $model->getAttributeLabel('values'))
        ->hint($hint);
    ?>
<?php endif;?>

<?php if ($originModel->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('@concepture/yii2handbook/views/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => $attribute,
        'originModel' => isset($originModel) ? $originModel : null,
        'label' => $label ?? $model->getAttributeLabel('values'),
        'hint' => $hint
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
        )
        ->hint($hint);
    ?>
<?php endif;?>