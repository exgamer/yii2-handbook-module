<?php

use yii\helpers\Html;
use \concepture\yii2handbook\models\SeoSettings;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;

$this->setTitle(Yii::t('yii2admin', 'Редактирование'));
$this->pushBreadcrumbs(['label' => SeoSettings::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(['index'], SeoSettings::label(),'icon-list');
$saveButton = Html::submitButton(
    '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
    [
        'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
    ]
);

$saveRedirectButton = Html::submitButton(
    '<b><i class="icon-list"></i></b>' . Yii::t('yii2admin', 'Сохранить и перейти к списку'),
    [
        'class' => 'btn bg-info btn-labeled btn-labeled-left ml-1',
        'name' => RequestHelper::REDIRECT_BTN_PARAM,
        'value' => 'index'
    ]
);

?>

<?php Pjax::begin(['formSelector' => '#seo-settings-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'seo-settings-form']); ?>
        <div class="card">
            <div class="card-body text-right">
                <?= $saveRedirectButton; ?>
                <?= $saveButton; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($items as $key => $item) :?>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <?= $this->render('include/_miltiple_items', [
                                'form' => $form,
                                'model' => $model,
                                'attribute' => $model->normalizeAttribute($item->name),
                                'label' => $item->caption,
                                'originModel' => $item,
                            ]) ?>
                            <?= $form->field($model, 'ids[]', ['template' => '{input}'])->hiddenInput(['value' => $item->id]);?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-body text-right">
                <?= $saveRedirectButton; ?>
                <?= $saveButton; ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>


