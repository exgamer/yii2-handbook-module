<?php

$this->setTitle(Yii::t('user', 'Новая запись'));
$this->pushBreadcrumbs(['label' => Yii::t('handbook', 'Сущности'), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(['index'], Yii::t('handbook', 'Сущности'),'icon-list');
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
