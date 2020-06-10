<?php

use yii\helpers\Html;
use yii\helpers\Url;
use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;
use concepture\yii2handbook\v2\models\DynamicElements;

$saveButton = Html::saveButton();
$saveRedirectButton = Html::saveRedirectButton();

?>

<?php Pjax::begin(['formSelector' => '#dynamic-elements-form']); ?>
    <?php $form = ActiveForm::begin(['id' => 'dynamic-elements-form', 'model' => new DynamicElements()]); ?>
        <div class="d-md-flex align-items-md-start">
            <?= $this->render('_domains_sidebar', [
                    'domainsData' => $domainsData,
                    'domain_id' => $domain_id,
                    'url' => ['update', 'id' => $originModel->id]
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
                        <?php if(isset($originModel)) :?>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                <?= $originModel->caption;?>
                            </legend>
                        <?php endif ;?>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <?= $this->render('@concepture/yii2handbook/views/include/_value_field', [
                                    'form' => $form,
                                    'model' => $model,
                                ]) ?>
                            </div>
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
        </div>
    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
