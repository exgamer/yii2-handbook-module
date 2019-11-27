<?php

use yii\helpers\Html;

$this->title = Yii::t('backend', 'Редактировать: {name}', [
    'name' => $originModel->caption,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Теги'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $originModel->id, 'url' => ['view', 'id' => $originModel->id]];
$this->params['breadcrumbs'][] = Yii::t('user', 'Редактировать');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
