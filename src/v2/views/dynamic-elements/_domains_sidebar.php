<?php
    use yii\helpers\Html;
?>

<div class="sidebar bg-transparent sidebar-component-left border-0 shadow-0 sidebar-expand-lg sidebar-expand-md" style="">
    <div class="sidebar-content">
        <div class="card">
            <div class="card-header bg-transparent header-elements-inline">
                            <span class="card-title font-weight-semibold">
                                <?= Yii::t('yii2admin', 'Версии');?>
                            </span>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-tabs-vertical flex-column border-bottom-0">
                    <?php foreach ($domainsData as $id => $data) :?>
                        <?php $active = ($domain_id == $id ? 'active' : "");?>
                        <?php $url['domain_id'] = $id;?>
                        <li class="nav-item">
                            <?= Html::a(
                                '<span class="icon flag-' . $data['country'] . ' flag"></span>'. $data['country_caption']. " (" . $data['country'] . ")",
                                $url,
                                [
                                    'class' => "nav-link {$active}",
                                ]
                            ) ?>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
</div>