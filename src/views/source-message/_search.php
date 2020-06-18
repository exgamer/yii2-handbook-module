<?php
    use concepture\yii2logic\enum\AccessEnum;
?>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'message')->textInput();?>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'translation')->textInput();?>
    </div>
    <?php if (!\Yii::$app->user->can(AccessEnum::SUPERADMIN)) { ?>
    <div class="col-lg-4 col-md-6 col-sm-12">
        <?= $form->field($model,'category')->textInput();?>
    </div>
    <?php } ?>
</div>