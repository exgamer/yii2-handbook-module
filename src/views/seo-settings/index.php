<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use concepture\yii2handbook\converters\LocaleConverter;

$this->title = Yii::t('backend', 'Настройки SEO');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('user', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'url',
            'seo_h1',
            'seo_title',
            'seo_description',
            'seo_keywords',
            [
                'attribute'=>'locale',
                'filter'=> Yii::$app->localeService->catalog(),
                'value'=>function($data) {
                    return LocaleConverter::value($data->locale);
                }
            ],
            [
                'attribute'=>'domain_id',
                'filter'=> Yii::$app->domainService->catalog(),
                'value'=>function($data) {
                    return $data->getDomainName();
                }
            ],
            'created_at',
            'updated_at',

            [
                'class'=>'yii\grid\ActionColumn',
                'template'=>'{view} {update}',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
