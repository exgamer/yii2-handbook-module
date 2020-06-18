<?php

use concepture\yii2handbook\enum\SettingsTypeEnum;
use concepture\yii2handbook\v2\models\DynamicElements;

$hint = null;
if(isset($originModel) && $originModel instanceof DynamicElements && $originModel->value_params) {
    $hint = Yii::$app->dynamicElementsService->getValueParamsHint($originModel);
}

?>
<?php if ($model->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form->field($model, 'value')->textInput(['maxlength' => true])->hint($hint) ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form->field($model, 'value')->textarea()->hint($hint); ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('@concepture/yii2handbook/views/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => 'value',
        'originModel' => isset($originModel) ? $originModel : null,
        'hint' => $hint
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
        )->hint($hint);
    ?>
<?php endif;?>
