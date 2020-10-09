<?php

use yii\helpers\ArrayHelper;

$messageCategoryEnumClass = Yii::$app->sourceMessageService->messageCategoryEnumClass;
$domainsData = Yii::$app->domainService->getDomainsData();
$languagesDropdown = ArrayHelper::map(
        $domainsData,
        function($item) {
            return "{$item['language']}-{$item['country']}";
        },
        'country_caption'
);

?>
<div class="row">
    <div class="col-lg-4 ?> col-md-6 col-sm-12">
        <?= $form->field($model,'message')->textInput();?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'translation')->textInput();?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form
            ->field($model,'category')
            ->dropDownList($messageCategoryEnumClass::arrayList(), [
                'class' => 'form-control custom-select',
                'prompt' => ''
            ]);
        ?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form
            ->field($model,'messageLanguage')
            ->dropDownList($languagesDropdown, [
                'class' => 'form-control custom-select',
                'prompt' => ''
            ]);
        ?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form
            ->field($model, 'is_empty', [
                'template' => '
                    <div class="form-check form-check-inline mt-4">
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