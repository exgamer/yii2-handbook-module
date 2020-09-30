<?php

use concepture\yii2handbook\enum\MessageCategoryEnum;

$messageCategoryEnumClass = Yii::$app->sourceMessageService->messageCategoryEnumClass;

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
</div>