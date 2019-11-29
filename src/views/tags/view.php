<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\enum\IsDeletedEnum;

/* @var $this yii\web\View */
/* @var $model concepture\user\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Теги'), 'url' => ['index']];
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
            'caption',
            'description',
            [
                'attribute'=>'user_id',
                'value'=>function($data) {
                    return $data->getUserName();
                }
            ],
            [
                'attribute'=>'type',
                'value'=>function($data) {
                    return $data->getTypeLabel();
                }
            ],
            'created_at',
            'updated_at',
            [
                'attribute'=>'is_deleted',
                'filter'=> IsDeletedEnum::arrayList(),
                'value'=>function($data) {
                    return $data->isDeletedLabel();
                }
            ],
        ],
    ]) ?>

</div>
