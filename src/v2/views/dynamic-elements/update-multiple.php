<?php

use yii\helpers\Html;
use \concepture\yii2handbook\models\DynamicElements;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use kamaelkz\yii2admin\v1\modules\audit\services\AuditService;
use kamaelkz\yii2admin\v1\modules\audit\actions\AuditDynamicElementsAction;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;

$this->setTitle(Yii::t('yii2admin', 'Редактирование'));
$this->pushBreadcrumbs(['label' => DynamicElements::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(['index'], DynamicElements::label(),'icon-list');

if (AuditService::isAuditAllowed(DynamicElements::class)) {
    $this->viewHelper()->pushPageHeader(
        [AuditDynamicElementsAction::actionName(), 'ids' => Yii::$app->request->get('ids')],
        Yii::t('yii2admin', 'Аудит'),
        'icon-eye'
    );
}

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

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'dynamic-elements-form', 'model' => new DynamicElements()]); ?>
        <div class="d-md-flex align-items-md-start">
            <?= $this->render('_domains_sidebar', [
                'domainsData' => $domainsData,
                'domain_id' => $domain_id,
                'url' => ['update-multiple', 'ids' => $ids]
            ]);
            ?>
            <div class="w-100">
                <div class="card">
                    <div class="card-body text-right">
                        <?= $saveRedirectButton; ?>
                        <?= $saveButton; ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php $generalHeader = false;?>
                            <?php foreach ($items as $key => $item) :?>
                                <div id="<?= $item->name;?>" class="col-lg-12 col-md-12 col-sm-12">
                                    <?php if($key === 0 && $item->general == false):?>
                                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                                            <?= Yii::t('yii2handbook', 'По адресу') ;?>
                                        </legend>
                                    <?php endif;?>
                                    <?php if($generalHeader == false && $item->general == true) :?>
                                        <?php $generalHeader = true;?>
                                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                                            <?= Yii::t('yii2handbook', 'Общие') ;?>
                                        </legend>
                                    <?php endif;?>
                                    <?= $this->render('_value_field', [
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
                </div>
                <div class="card">
                    <div class="card-body text-right">
                        <?= $saveRedirectButton; ?>
                        <?= $saveButton; ?>
                    </div>
                </div>
            </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>


