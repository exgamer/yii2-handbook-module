<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use concepture\yii2handbook\converters\LocaleConverter;


$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Настройк SEOи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('user', 'Редактировать'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'url',
            'seo_h1',
            'seo_title',
            'seo_description',
            'seo_keywords',
            [
                'attribute'=>'locale',
                'value'=>function($data) {

                    return LocaleConverter::value($data->locale);
                }
            ],
            [
                'attribute'=>'domain_id',
                'value'=>$model->getDomainName(),
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
