<?php
use yii\helpers\Html;
?>

<?php if (Yii::$app->localeService->catalogCount() > 1): ?>
    <ul class="nav nav-tabs nav-tabs-solid nav-justified bg-light">
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
<?php endif; ?>