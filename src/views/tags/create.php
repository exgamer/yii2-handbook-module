<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model concepture\user\models\User */

$this->title = Yii::t('backend', 'Добавить тег');
$this->params['breadcrumbs'][] = ['label' => Yii::t('handbook', 'Теги'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
