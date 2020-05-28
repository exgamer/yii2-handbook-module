<?php

use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2handbook\enum\MenuEnum;

?>

<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'id')->textInput();?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'caption')->textInput();?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form
            ->field($model, 'status')
            ->dropDownList(StatusEnum::arrayList(), [
                'class' => 'form-control form-control-uniform active-form-refresh-control',
                'prompt' => ''
            ]);
        ?>
    </div>
</div>
