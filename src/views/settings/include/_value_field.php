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

<?php if ($model->type == SettingsTypeEnum::FROALA) : ?>
    <?= $form
        ->field($model, 'value')
        ->widget(FroalaEditor::class, [
            'model' => $model,
            'attribute' => 'editor',
            'clientOptions' => [
                'attribution' => false,
                'heightMin' => 200,
                'toolbarSticky' => true,
                'toolbarInline'=> false,
                'theme' =>'royal', //optional: dark, red, gray, royal
                'language' => Yii::$app->language,
                'quickInsertTags' => [],
            ]
        ]);
    ?>
<?php endif;?>
