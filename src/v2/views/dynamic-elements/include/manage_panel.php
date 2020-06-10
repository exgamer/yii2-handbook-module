<?php
    use yii\helpers\Url;
    use yii\helpers\Html;
?>
<div class="yii2-handbook-dynamic-elements-manage-panel">
    <div class="yii2-handbook-dynamic-elements-manage-panel__toggle" onclick="document.querySelector('.yii2-handbook-dynamic-elements-manage-panel').classList.toggle('yii2-handbook-dynamic-elements-manage-panel_expanded')">

    </div>
    <ul class="yii2-handbook-dynamic-elements-manage-panel__items">
        <li>
            <a href="<?= $url;?>" target="_blank">
                <?= Yii::t('handbook', 'Динамические элементы')?> (<?= $count;?>)
            </a>
        </li>
        <li>
            <a>
                <?= Html::checkbox(null,$interactiveMode, ['data-url' => Url::to(['/admin/handbook/dynamic-elements/interactive-mode'])]);?>
            </a>
        </li>
    </ul>
</div>
