<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();
?>

<?php Pjax::begin(['formSelector' => '#entity-type-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'entity-type-form']); ?>
        <div class="card">
            <div class="card-body text-right">
                <?=  $saveRedirectButton?>
                <?=  $saveButton?>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form->field($model, 'table_name')->textInput(['maxlength' => true, 'disabled' => isset($originModel) ? true : false]) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form
                            ->field($model, 'sort_module', [
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
                                    'labelOptions' => ['class' => 'form-check-label control-label']
                                ],
                                true
                            )
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-right">
                <?=  $saveRedirectButton?>
                <?=  $saveButton?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
