<?php

use concepture\yii2logic\enum\AccessEnum;
use concepture\yii2handbook\enum\MessageCategoryEnum;

$super_admin = false;
if (\Yii::$app->user->can(AccessEnum::SUPERADMIN)) {
    $super_admin = true;
}

?>
<div class="row">
    <div class="col-lg-<?=$super_admin ? 4 : 6 ?> col-md-6 col-sm-12">
        <?= $form->field($model,'message')->textInput();?>
    </div>
    <div class="col-lg-<?=$super_admin ? 4 : 6 ?> col-md-6 col-sm-12">
        <?= $form->field($model,'translation')->textInput();?>
    </div>
    <?php if($super_admin){ ?>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <?= $form
                ->field($model,'category')
                ->dropDownList(MessageCategoryEnum::arrayList(), [
                    'class' => 'form-control custom-select',
                    'prompt' => ''
                ]);
            ?>
        </div>
    <?php } ?>
</div>