<?php

use yii\helpers\Html;
use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use concepture\yii2handbook\models\DynamicElements;

$saveButton = Html::saveButton();
$saveRedirectButton = Html::saveRedirectButton();

?>

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'dynamic-elements-form', 'model' => new DynamicElements()]); ?>
        <?php if(isset($originModel)) :?>
            <legend class="font-weight-semibold text-uppercase font-size-sm">
                <?= $originModel->caption;?>
            </legend>
        <?php endif ;?>
        <div class="card">
<!--            <div class="card-body">-->
<!--                <div class="row">-->
<!--                    <div class="col-lg-6 col-md-6 col-sm-12">-->
<!--                        --><?//= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-6 col-md-6 col-sm-12">-->
<!--                        --><?//= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-6 col-md-6 col-sm-12">-->
<!--                        --><?//= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
<!--                    </div>-->
<!--                    <div class="col-lg-6 col-md-6 col-sm-12">-->
<!--                        --><?//= $form
//                            ->field($model, 'type')
//                            ->dropDownList(SettingsTypeEnum::arrayList(), [
//                                'class' => 'form-control form-control-uniform active-form-refresh-control',
//                                'prompt' => ''
//                            ]);
//                        ?>
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?= $this->render('../include/_value_field', [
                            'form' => $form,
                            'model' => $model,
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="card-body text-right">
                <?= $saveRedirectButton; ?>
                <?= $saveButton; ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
