<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use concepture\yii2handbook\models\Message;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\plural\Plural;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use \concepture\yii2handbook\enum\DeclinationFormatEnum;

$this->setTitle(Yii::t('yii2admin', 'Редактирование'));
$this->pushBreadcrumbs(['label' => Message::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);

$this->registerJs('
    $(document).on("click", ".source-message-copy", function () {
        let $form = $(this).closest(".tab-pane");
        if ($form.length === 0) {
            return;
        }
        let $firstInput = $form.find("input[type=\"text\"]").first();
        if ($firstInput.length === 0) {
            return;
        }
        
        let value = $firstInput.val();
        $(this).parents(".input-group").find("input[type=\"text\"]").val(value);
        
        let $pluralsForCopy = $firstInput.parents(".translate-inputs").find(".plurals input[type=\"text\"]");
        let $plurals = $(this).parents(".translate-inputs").find(".plurals input[type=\"text\"]");
        
        if ($pluralsForCopy.length !== 0 && $plurals.length !== 0) {
            $pluralsForCopy.each(function(index) {
//                console.log(index, $plurals.get(index));
                $plurals.eq(index).val($(this).val())
            });
        }
    });
',
    \yii\web\View::POS_END,
    'source-message-copy-handler'
);

$saveButton = Html::saveButton();
$saveRedirectButton = Html::saveRedirectButton();


?>

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form', 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin(['id' => 'dynamic-elements-form']); ?>
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-transparent header-elements-inline">
                <span class="card-title font-weight-semibold">
                    <?= Yii::t('yii2admin','Язык');?>
                </span>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-tabs-vertical flex-column border-bottom-0">
                    <?php foreach (array_keys($itemsByLanguage) as $lang): ?>
                        <li class="nav-item">
                            <?php
                            $iso = $lang;
                            if(strpos($lang, '-') !== false) {
                                list($iso,) = explode('-', $lang);
                            }
                            ?>
                            <?= Html::a(
                                $languages[$iso]->caption,
                                "#tab-{$lang}",
                                [
                                    'class' => 'nav-link ' . ('ru' === $lang ? 'active' : null),
                                    'data-toggle' => 'tab',
                                ]
                            ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="tab-content col-md-9">
        <div class="card">
            <div class="card-body text-right">
                <?= $saveRedirectButton; ?>
                <?= $saveButton; ?>
            </div>
        </div>
        <?php foreach ($itemsByLanguage as $lang => $items): ?>
            <div class="tab-pane fade <?= 'ru' === $lang ? 'active' : null;?> show" id="tab-<?= $lang; ?>" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <?= Yii::t('yii2admin','Оригинал');?> :
                            <?php $isPlural = false; ?>
                            <?php if (preg_match('/(plural)/', $sourceMessage->message)): ?>
                                <?php $isPlural = true; ?>
                                <?= preg_replace('/{.*}/', '{plural}', $sourceMessage->message); ?>
                            <?php else: ?>
                                <?= $sourceMessage->message ;?>
                            <?php endif; ?>
                        </legend>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if ($isPlural): ?>
                                <div class="card alpha-success border-success" style="margin-left: 46px;">
                                    <div class="card-body">
                                        <?= $this->render('@yii2admin/widgets/formelements/plural/views/hint');?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php $i = 0; ?>

                            <?php foreach ($items as $key => $item) :?>
                                <div id="<?= $item->language;?>" class="col-lg-12 col-md-12 col-sm-12">
                                    <?php
                                    $itemLang = $item->language;
                                    if(strpos($itemLang, '-') !== false) {
                                        list($languageIso, $itemLang) = explode('-', $itemLang);

                                    }

                                    $label = $countries[$itemLang]->caption ?? null;
                                    $language = strtoupper($itemLang);
                                    if($label) {
                                        $label = "{$label} ({$language})";
                                    } else {
                                        $label = $language;
                                    }
                                    $label = '<span class="icon flag-' . $countries[$itemLang]->iso . ' flag"></span>' . $label;
                                    ?>
                                    <?php
                                    // TODO очень похоже на костыль
                                    $copyBtn = '
                                    <span class="source-message-copy btn bg-transparent text-black" style="cursor: pointer;">
                                        <i class="icon-copy4"></i>
                                    </span>
                                ';
                                    if ($i == 0) {
                                        $copyBtn = '
                                        <span class="btn bg-transparent text-white"">
                                            <i class="icon-copy4"></i>
                                        </span>
                                    ';
                                    }
                                    ?>
                                    <?php
                                    $formName = $model->formName();
                                    $inputId = Inflector::underscore($formName . "_{$item->language}");
                                    ?>
                                    <div class="translate-inputs">
                                        <div style="margin-left: 46px">
                                            <label class="control-label" for="<?= $inputId;?>">
                                                <?= $label;?>
                                            </label>
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                                <?= $copyBtn;?>
                                            </span>
                                            <?php
                                            $disabled = false;
                                            if (! \Yii::$app->user->hasDomainAccessByCountry($countries[$itemLang]->id ?? null)) {
                                                $disabled = true;
                                            }
                                            ?>

                                            <?= Html::textInput(
                                                "{$formName}[$item->language]",
                                                ! $isPlural ? $item->translation : preg_replace('/{n, plural, \S\w*{.*}/', '{plural}', $item->translation),
                                                [
                                                    'id' => $inputId,
                                                    'class' => 'form-control',
                                                    'disabled' => $disabled
                                                ]
                                            );
                                            ?>
                                        </div>
                                        <?= $form->field($model, 'ids[]', ['template' => '{input}'])->hiddenInput(['value' => $item->id]);?>
                                        <?= $form->field($model, 'languages[]', ['template' => '{input}'])->hiddenInput(['value' => $item->language]);?>
                                        <?php if ($isPlural): ?>
                                            <div class="plurals" style="margin-left: 92px">
                                                <?= Plural::widget([
                                                    'form' => $form,
                                                    'model' => $model,
                                                    'originText' => $model->originText,
                                                    'pluralAttr' => 'plurals',
                                                    'targetAttr' => $item->language,
                                                    'token' => '{plural}',
                                                    'declination_format' => $languages[($languageIso ?? $lang)]->declination_format ?? DeclinationFormatEnum::FULL,
                                                    'disabled' => $disabled
                                                ]); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="card">
            <div class="card-body text-right">
                <?= $saveRedirectButton; ?>
                <?= $saveButton; ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>




