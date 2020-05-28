<?php

use yii\helpers\Html;
use concepture\yii2handbook\enum\MenuEnum;
use concepture\yii2handbook\enum\IconEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\widgets\formelements\dynamicform\v2\DynamicForm;

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();

?>

<?php  Pjax::begin(['formSelector' => '#menu-form']); ?>
<?php  $form = ActiveForm::begin(['id' => 'menu-form']); ?>
<div class="card">
    <div class="card-body text-right">
        <?=  $saveRedirectButton?>
        <?=  $saveButton?>
    </div>
    <div class="card">
        <div class="card-body">
            <legend class="font-weight-semibold text-uppercase font-size-sm">
                <?= Yii::t('yii2admin', 'Основные данные') ;?>
            </legend>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <?= $form
                        ->field($model, 'type')
                        ->dropDownList(MenuEnum::arrayList(), [
                            'class' => 'form-control form-control-uniform',
                            'prompt' => ''
                        ]);
                    ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <?= $form->field($model, 'caption')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <legend class="font-weight-semibold text-uppercase font-size-sm">
                <?= Yii::t('yii2admin', 'Дополнительные данные') ;?>
            </legend>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <?= $form->field($model, 'desktop_max_count')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <?= $form->field($model, 'link_all_caption')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12">
                    <?= $form->field($model, 'link_all_url')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <legend class="font-weight-semibold text-uppercase font-size-sm">
                <?= Yii::t('yii2admin', 'Пункты меню') ;?>
            </legend>
            <div class="mb-3">
                <?= DynamicForm::widget([
                    // 'limit' => 20, // the maximum times, an element can be cloned (default 999)
                    'min' => empty($model->items) ? 0 :1, // 0 or 1 (default 1)
                    'form' => $form,
                    'models' => $model->getPojoModels('items'),
                    'dragAndDrop' => true,
                    'formId' => $form->getId(),
                    'attributes' => [
                        'text' => [
                            'type' => Html::FIELD_TEXT_INPUT,
                        ],
                        'url' => [
                            'type' => Html::FIELD_TEXT_INPUT,
                        ],
                        'icon' => [
                            'type' => Html::FIELD_DROPDOWN,
                            'params' => [
                                IconEnum::arrayList(),
                                [
                                    'class' => 'form-control custom-select',
                                    'prompt' => ''
                                ]
                            ]
                        ],
                    ]
                ]); ?>
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


    <div class="card-body text-right">
        <?=  $saveRedirectButton?>
        <?=  $saveButton?>
    </div>
</div>
<?php  ActiveForm::end(); ?>
<?php  Pjax::end(); ?>



