<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
?>

<?php Pjax::begin(['formSelector' => '#seo-settings-form']); ?>

<?php $form = ActiveForm::begin(['id' => 'seo-settings-form']); ?>
<div class="card">
    <div class="card-body text-right">
        <?=  Html::submitButton(
            '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
            [
                'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
            ]
        ); ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'seo_h1')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'seo_description')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'seo_keywords')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <?= $form
                    ->field($model, 'seo_text')
                    ->widget(FroalaEditor::class, [
                        'model' => $model,
                        'attribute' => 'seo_text',
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
            </div>
<!--            <div class="col-lg-12 col-md-12 col-sm-12">-->
<!--                --><?//= $form->field($model, 'seo_text')->textarea(); ?>
<!--            </div>-->
        </div>
    </div>
    <div class="card-body text-right">
        <?=  Html::submitButton(
            '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
            [
                'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
            ]
        ); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
