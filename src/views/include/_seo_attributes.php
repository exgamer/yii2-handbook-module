<legend class="font-weight-semibold text-uppercase font-size-sm">
    <?= Yii::t('yii2handbook', 'SEO') ;?>
</legend>
<div class="row">
    <?php if(! isset($except['seo_name'])) :?>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <?= $form->field($model, 'seo_name')->textInput(['maxlength' => true, 'disabled' => isset($originModel) ? true : false]) ?>
        </div>
    <?php endif;?>
    <?php if(! isset($except['seo_title'])) :?>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?= $form->field($model, 'seo_title')->textInput(['maxlength' => true]) ?>
        </div>
    <?php endif;?>
</div>
<div class="row">
    <?php if(! isset($except['seo_description'])) :?>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <?= $form->field($model, 'seo_description')->textarea(['rows' => 5]); ?>
        </div>
    <?php endif;?>
    <?php if(! isset($except['seo_description'])) :?>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <?= $form->field($model, 'seo_keywords')->textarea(['rows' => 5]); ?>
        </div>
    <?php endif;?>
</div>