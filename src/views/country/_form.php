<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2cdnuploader\enum\StrategiesEnum;
use kamaelkz\yii2cdnuploader\widgets\CdnUploader;
$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();

?>

<?php Pjax::begin(['formSelector' => '#locale-form']); ?>

<?php $form = ActiveForm::begin(['id' => 'locale-form']); ?>
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
                    <?= $form->field($model, 'iso')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form
                        ->field($model, 'locale')
                        ->dropDownList(Yii::$app->localeService->catalog(), [
                            'class' => 'form-control custom-select',
                            'prompt' => Yii::t('banner', 'Выберите язык')
                        ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form
                        ->field($model, 'image')
                        ->widget(CdnUploader::class, [
                            'model' => $model,
                            'attribute' => 'image',
                            'strategy' => StrategiesEnum::BY_REQUEST,
                            'resizeBigger' => false,
    //                            'width' => 768,
    //                            'height' => 312,
                            'options' => [
                                'plugin-options' => [
                                    # todo: похоже не пашет
                                    'maxFileSize' => 2000000,
                                ]
                            ],
                            'clientEvents' => [
                                'fileuploaddone' => new \yii\web\JsExpression('function(e, data) {
                                                        console.log(e);
                                                    }'),
                                'fileuploadfail' => new \yii\web\JsExpression('function(e, data) {
                                                        console.log(e);
                                                    }'),
                            ],
                        ])
                        ->error(false)
                        ->hint(false);
                    ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <?= $form
                        ->field($model, 'image_anons')
                        ->widget(CdnUploader::class, [
                            'model' => $model,
                            'attribute' => 'image_anons',
                            'strategy' => StrategiesEnum::BY_REQUEST,
                            'resizeBigger' => false,
    //                            'width' => 535,
    //                            'height' => 321,
                            'options' => [
                                'plugin-options' => [
                                    # todo: похоже не пашет
                                    'maxFileSize' => 2000000,
                                ]
                            ],
                            'clientEvents' => [
                                'fileuploaddone' => new \yii\web\JsExpression('function(e, data) {
                                                        console.log(e);
                                                    }'),
                                'fileuploadfail' => new \yii\web\JsExpression('function(e, data) {
                                                        console.log(e);
                                                    }'),
                            ],
                        ])
                        ->error(false)
                        ->hint(false);
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
