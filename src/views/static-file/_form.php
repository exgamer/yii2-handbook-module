<?php

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
$saveRedirectButton = Html::submitButton(
    '<b><i class="icon-list"></i></b>' . Yii::t('yii2admin', 'Сохранить и перейти к списку'),
    [
        'class' => 'btn bg-info btn-labeled btn-labeled-left ml-1',
        'name' => \kamaelkz\yii2admin\v1\helpers\RequestHelper::REDIRECT_BTN_PARAM,
        'value' => 'index'
    ]
);
$saveButton = Html::submitButton(
    '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
    [
        'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
    ]
);
?>

<?php Pjax::begin(['formSelector' => '#robots-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'static-page-form']); ?>
        <div class="card">
            <div class="card-body text-right">
                <?=  $saveRedirectButton?>
                <?=  $saveButton?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?= $form
                            ->field($model, 'extension')
                            ->dropDownList(FileExtensionEnum::labels(), [
                                'class' => 'form-control form-control-uniform',
                                'prompt' => Yii::t('yii2admin', 'Выберите тип фаила')
                            ]);
                        ?>
                    </div>
<!--                    <div class="col-lg-12 col-md-12 col-sm-12">-->
<!--                        --><?//= $form
//                            ->field($model, 'type')
//                            ->dropDownList(StaticFileTypeEnum::labels([StaticFileTypeEnum::SITEMAP, StaticFileTypeEnum::ROBOTS]), [
//                                'class' => 'form-control form-control-uniform',
//                                'prompt' => Yii::t('yii2admin', 'Выберите тип фаила')
//                            ]);
//                        ?>
<!--                    </div>-->
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
                <?=  $saveRedirectButton?>
                <?=  $saveButton?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>