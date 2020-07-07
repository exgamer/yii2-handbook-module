<?php

use concepture\yii2logic\helpers\AccessHelper;
use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use concepture\yii2handbook\v2\models\DynamicElements;
use concepture\yii2logic\enum\AccessEnum;
use \concepture\yii2handbook\v2\enum\DynamicElementsTypeEnum;

$saveButton = Html::saveButton();
$saveRedirectButton = Html::saveRedirectButton();
$is_superadmin = Yii::$app->getUser()->can(AccessEnum::SUPERADMIN);
$hasAccess = true;
if (! AccessHelper::checkCurrentRouteAccess(['domain_id' => $domain_id]) && isset($originModel)) {
    $hasAccess = false;
}
?>

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form', 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin(['id' => 'dynamic-elements-form', 'model' => new DynamicElements()]); ?>
<div class="d-md-flex align-items-md-start">
    <?= $this->render('@concepture/yii2handbook/views/include/_domains_sidebar', [
            'domain_id' => $domain_id,
            'model' => $originModel ?? null,
            'url' => ['update', 'id' => $originModel->id]
        ]);
    ?>
    <div class="w-100">
        <?php if ($hasAccess) :?>
            <div class="card">
                <div class="card-body text-right">
                    <?= $saveRedirectButton; ?>
                    <?= $saveButton; ?>
                </div>
            </div>
            <?= $form->errorSummary($originModel);?>
        <?php endif;?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <?php if($is_superadmin) :?>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <?= $form
                                ->field($model, 'type')
                                ->dropDownList(DynamicElementsTypeEnum::arrayList(), [
                                    'class' => 'form-control custom-select',
                                ]);
                            ?>
                        </div>
                        <!--                                <div class="col-lg-12 col-md-12 col-sm-12">-->
                        <!--                                    --><?//= $form->field($model, 'name')->textInput() ?>
                        <!--                                </div>-->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <?=
                            $form->field($model, 'caption')->textInput()
                            ?>
                        </div>
                    <?php else:?>
                        <?php if(isset($originModel)) :?>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                <?= Yii::t('de', $originModel->caption);?>
                            </legend>
                        </div>
                        <?php endif ;?>
                    <?php endif;?>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?= $this->render('@concepture/yii2handbook/views/include/_value_field', [
                            'form' => $form,
                            'model' => $model,
                            'originModel' => $originModel,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($hasAccess) :?>
            <div class="card">
                <div class="card-body text-right">
                    <?= $saveRedirectButton; ?>
                    <?= $saveButton; ?>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
