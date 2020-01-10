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
    <?= $this->render('/include/_editor.php', [
        'form' => $form,
        'model' => $model,
        'attribute' => $attribute,
        'originModel' => isset($originModel) ? $originModel : null,
        'label' => $label ?? $model->getAttributeLabel('values')
    ]) ?>
<?php endif;?>
