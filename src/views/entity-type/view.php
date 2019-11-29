<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use concepture\yii2handbook\converters\LocaleConverter;

/* @var $this yii\web\View */
/* @var $model concepture\user\models\User */

$this->title = $model->table_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Сущности'), 'url' => ['index']];
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
            'table_name',
            'caption',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
