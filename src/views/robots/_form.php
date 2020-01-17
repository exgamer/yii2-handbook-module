<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
?>

<?php Pjax::begin(['formSelector' => '#robots-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'static-page-form']); ?>
        <div class="card">
            <div class="card-body text-right">
                <?=  Html::submitButton(
                    '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
                    [
                        'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
                    ]
                ); ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?= $form->field($model, 'content')->textarea(['style' => 'min-height:350px']) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form
                            ->field($model, 'status', [
                                'template' => '
                                                <div class="form-check form-check-inline mt-2">
                                                    {input}
                                                </div>
                                                {error}
                                            '
                            ])
                            ->checkbox(
                                [
                                    'label' => Yii::t('yii2admin', 'Активировано'),
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