<?php
use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\editors\froala\FroalaEditor;
use kamaelkz\yii2cdnuploader\enum\StrategiesEnum;
use kamaelkz\yii2cdnuploader\widgets\CdnUploader;
use kamaelkz\yii2admin\v1\widgets\ {
    formelements\pickers\DatePicker,
    formelements\pickers\TimePicker
};

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();
?>

<?php Pjax::begin(['formSelector' => '#payment-system-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'payment-system-form', 'model' => $originModel ?? new \concepture\yii2handbook\models\PaymentSystem()]); ?>
        <div class="d-md-flex align-items-md-start">
            <?= $this->render('@concepture/yii2handbook/views/include/_locale_sidebar', [
                'model' => $model,
            ]); ?>
            <div class="w-100">
                <div class="card">
                    <div class="card-body text-right">
                        <?= $saveRedirectButton?>
                        <?= $saveButton?>
                    </div>
                </div>
                <?= $form->errorSummary($model);?>
                <div class="card">
                    <div class="card-body">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <?= Yii::t('yii2admin', 'Основные данные') ;?>
                        </legend>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <?= $form
                                    ->field($model, 'logo')
                                    ->widget(CdnUploader::class, [
                                        'model' => $model,
                                        'attribute' => 'logo',
                                        'strategy' => StrategiesEnum::TRUSTED,
                                        'resizeBigger' => false,
                                        'width' => 63,
                                        'height' => 39,
                                        'options' => [
                                            'plugin-options' => [
                                                'maxFileSize' => 2000000,
                                            ]
                                        ]
                                    ])
                                    ->error(false)
                                    ->hint(false);
                                ?>
                            </div>
                        </div>
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <?= Yii::t('yii2admin', 'Дополнительно') ;?>
                        </legend>
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
                </div>
                <div class="card">
                    <div class="card-body text-right">
                        <?= $saveRedirectButton?>
                        <?= $saveButton?>
                    </div>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
