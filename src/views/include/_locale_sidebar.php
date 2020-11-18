<?php
use yii\helpers\Html;
?>

<?= Html::activeHiddenInput($model, 'locale'); ?>
<?php if (Yii::$app->localeService->catalogCount() > 1): ?>
    <div class="sidebar bg-transparent sidebar-secondary sidebar-component-left border-0 shadow-0 sidebar-expand-lg sidebar-expand-md" style="">
        <div class="sidebar-content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">
                        <?= Yii::t('yii2admin', 'Языки');?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-tabs-vertical flex-column border-bottom-0">
                        <?php $locale_id = Yii::$app->localeService->getCurrentLocaleId(); ?>
                        <?php foreach (Yii::$app->localeService->getByDomainMap() as $key => $locale):?>
                            <li class="nav-item">
                                <?= Html::a(
                                    Yii::$app->localeService->catalogValue($locale, 'locale', 'caption', function (\concepture\yii2logic\db\ActiveQuery $query) use ($locale_id) {
                                        $query->resetCondition();
                                        $query->where(['p.locale_id' => $locale_id])->active();
                                    }),
                                    \yii\helpers\Url::current(['locale' => $key]),
                                    ['class' => 'nav-link ' . ($key ==  $model->locale   ? "active" : "")]
                                ) ?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>