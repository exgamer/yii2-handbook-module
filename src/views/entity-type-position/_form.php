<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;

$entityTypes = Yii::$app->entityTypeService->getDropdownList();

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();

?>

<?php Pjax::begin(['formSelector' => '#entity-type-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'entity-type-form']); ?>
        <div class="card">
            <div class="card-body text-right">
                <?= $saveRedirectButton?>
                <?= $saveButton?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?= $form->field($model, 'alias')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                    </div>
                    <?php if(! isset($entity_type_id)) :?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <?=
                                $form->field($model, 'entity_type_id')->dropDownList(
                                    $entityTypes,
                                    [
                                        'class' => 'form-control form-control-uniform',
                                        'prompt' => ''
                                    ]
                                )
                            ?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="row">
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
            <div class="card-body text-right">
                <?= $saveRedirectButton?>
                <?= $saveButton?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
