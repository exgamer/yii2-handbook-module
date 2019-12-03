<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
?>

<?php Pjax::begin(['formSelector' => '#settings-form']); ?>

<?php $form = ActiveForm::begin(['id' => 'settings-form']); ?>
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
                <?= $form
                    ->field($model, 'type')
                    ->dropDownList(\concepture\yii2handbook\enum\SettingsTypeEnum::arrayList(), [
                        'class' => 'form-control form-control-uniform active-form-refresh-control',
                        'prompt' => ''
                    ]);
                ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <?= $this->render('include/_value_field', [
                    'form' => $form,
                    'model' => $model,
                ]) ?>
            </div>
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
    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>
