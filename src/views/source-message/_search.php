<?php
    use concepture\yii2logic\enum\AccessEnum;

    $admin = false;
    if (\Yii::$app->user->can(AccessEnum::SUPERADMIN)) {
        $admin = true;
    }
?>
<div class="row">
    <div class="col-lg-<?=$admin ? 4 : 6 ?> col-md-6 col-sm-12">
        <?= $form->field($model,'message')->textInput();?>
    </div>
    <div class="col-lg-<?=$admin ? 4 : 6 ?> col-md-6 col-sm-12">
        <?= $form->field($model,'translation')->textInput();?>
    </div>
    <?php if($admin){ ?>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <?= $form->field($model,'category')->textInput();?>
        </div>
    <?php } ?>
</div>