<?php

use yii\helpers\Html;
use concepture\yii2handbook\models\Message;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\plural\Plural;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;

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
                            <?= Html::a(
                                $languages[$lang]->caption,
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
                                        <ul style="margin-bottom: 0">
                                            <li>В поле для множественного числа нужно добавить токен {plural}</li>
                                            <li>{plural} будет заменен на одно из заполненных полей в зависимости от числа</li>
                                            <li>one — 21, 31, 41 и так далее;</li>
                                            <li>few — от 2 до 4, от 22 до 24 и так далее;</li>
                                            <li>many — 0, от 5 до 20, от 25 до 30 и так далее;</li>
                                            <li>Решётка # заменяется на цифру (Например: # отзывов)</li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>


                            <?php $i = 0; ?>
                            <?php foreach ($items as $key => $item) :?>
                                <div id="<?= $item->language;?>" class="col-lg-12 col-md-12 col-sm-12">
                                    <?php
                                    $label = $countries[$item->language]->caption ?? null;
                                    $language = strtoupper($item->language);
                                    if($label) {
                                        $label = "{$label} ({$language})";
                                    } else {
                                        $label = $language;
                                    }
                                    $label = '<span class="icon flag-' . $countries[$item->language]->iso . ' flag"></span>' . $label;
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
                                    <div class="translate-inputs">
                                        <?=
                                        $form
                                            ->field($model, $item->language, [
                                                'template' => '
                                                <div style="margin-left: 46px">{label}</div>
                                                <div class="input-group">
                                                    <span class="input-group-prepend">'.$copyBtn .'</span>
                                                    {input}
                                                </div>
                                                <div style="margin-left: 46px">{error}</div>
                                            '
                                            ])
                                            ->textInput(['value' => !$isPlural ? $item->translation : preg_replace('/{n, plural, \S\w*{.*}/', '{plural}', $item->translation)])
                                            ->label($label);
                                        ?>
                                        <?= $form->field($model, 'ids[]', ['template' => '{input}'])->hiddenInput(['value' => $item->id]);?>
                                        <?= $form->field($model, 'languages[]', ['template' => '{input}'])->hiddenInput(['value' => $item->language]);?>

                                        <?php if ($isPlural): ?>
                                            <div class="plurals" style="margin-left: 92px">
                                                <?= Plural::widget([
                                                    'form' => $form,
                                                    'model' => $model,
                                                    'originText' => $model->originText,
                                                    'pluralAttr' => 'plurals',
                                                    'targetAttr' => $countries[$item->language]->iso,
                                                    'token' => '{plural}',
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




