<?php
    use yii\helpers\Url;
    use yii\helpers\Html;
?>
<div class="yii2-handbook-seo-manage-panel">
    <ul>
        <li>
            <a href="<?= $url;?>" target="_blank">
                <?= Yii::t('handbook', 'seo настройки')?> (<?= $count;?>)
            </a>
        </li>
        <li style="margin-left: 10px;">
            <?= Html::checkbox(null,$interactiveMode, ['data-url' => Url::to(['/admin/handbook/seo-settings/interactive-mode'])]);?>
        </li>
    </ul>
</div>