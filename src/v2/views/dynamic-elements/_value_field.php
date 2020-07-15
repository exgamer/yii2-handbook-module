<?php

use yii\helpers\Html;
use concepture\yii2handbook\enum\SettingsTypeEnum;
use concepture\yii2handbook\v2\models\DynamicElements;
use concepture\yii2handbook\v2\enum\DynamicElementsTypeEnum;
use kamaelkz\yii2cdnuploader\widgets\CdnUploader;
use kamaelkz\yii2cdnuploader\enum\StrategiesEnum;

$hint = null;
if(isset($originModel) && $originModel instanceof DynamicElements && $originModel->value_params) {
    $hint = Yii::$app->dynamicElementsService->getValueParamsHint($originModel);
}

$badge = null;
if($originModel->multi_domain == false) {
    $badge = Html::tag('span', Yii::t('yii2admin', 'Только текущая версия'), ['class' => 'badge badge-flat border-info text-info-600']);
}

?>
<?php if ($model->type == SettingsTypeEnum::TEXT) : ?>
    <?= $form
        ->field($model, $attribute)->textInput([
            'maxlength' => true
        ])
        ->label($label ?? $model->getAttributeLabel('value'))
        ->hint($hint);
    ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_AREA) : ?>
    <?= $form
        ->field($model, $attribute)->textarea()
        ->label($label ?? $model->getAttributeLabel('value'))
        ->hint($hint);
    ?>
<?php endif;?>

<?php if ($model->type == SettingsTypeEnum::TEXT_EDITOR) : ?>
    <?= $this->render('@concepture/yii2handbook/views/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => $attribute,
        'originModel' => isset($originModel) ? $originModel : null,
        'label' => $label ?? $model->getAttributeLabel('value'),
        'hint' => $hint
    ]) ?>
<?php endif;?>
<?php if ($model->type == SettingsTypeEnum::CHECKBOX) : ?>
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
                'label' => $label ?? $model->getAttributeLabel('value')
            ],
            true
        )
        ->hint($hint);
    ?>
<?php endif;?>
<?php if ($model->type == DynamicElementsTypeEnum::IMAGE_UPLOADER) : ?>
    <?= $form
        ->field($model, $attribute)
        ->widget(CdnUploader::class, [
            'model' => $model,
            'attribute' => $attribute,
            'strategy' => StrategiesEnum::TRUSTED,
            'resizeBigger' => false,
            'options' => [
                'plugin-options' => [
                    'maxFileSize' => 2000000,
                ]
            ],
        ])
        ->label($label ?? $model->getAttributeLabel('value'))
        ->error(false)
        ->hint(false);
    ?>
<?php endif;?>
<div class="d-block" style="margin-top: -1rem !important">
    <?= $badge;?>
</div>
