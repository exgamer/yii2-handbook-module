<?php
use yii\helpers\Html;

Yii::$app->getView()->viewHelper()->setSecondSidebarState(true);
$allDomainsData = Yii::$app->domainService->getDomainsData();
$allDomainsData = \yii\helpers\ArrayHelper::index($allDomainsData, 'alias');
$languages = Yii::$app->domainService->getDomainsByLocales();
$domainsData = Yii::$app->domainService->getModelDomains($originModel ?? null);
if (! isset($locale_id)) {
    $locale_id = $domainsData[$domain_id]['language_id'];
}
// Если в domain-map не указаны используемые языки, выводим стандартный саидбар
if ( empty($languages)) {
    echo $this->render('@concepture/yii2handbook/views/include/_domains_sidebar', [
        'domain_id' => $domain_id,
        'locale_id' => $locale_id,
        'url' => $url,
        'originModel' => $originModel,
    ]);
    return;
}
?>
    ?>
<?php if(count($domainsData) > 0) :?>
    <div class="sidebar bg-transparent sidebar-secondary sidebar-component-left border-0 shadow-0 sidebar-expand-lg sidebar-expand-md" style="">
        <div class="sidebar-content" data-current-domain-id="<?= $domain_id;?>">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">
                        <?= Yii::t('yii2admin', 'Версии');?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-tabs-vertical flex-column border-bottom-0">
                        <?php foreach ($languages as $language_id => $data) :?>
                            <?php $usedDomainId = $data['used_domain_id'];?>
                            <?php

                            if (
                            ! \Yii::$app->user->hasDomainAccess($usedDomainId)
                            ) {
                                continue;
                            }
                            ?>
                            <?php $url['domain_id'] = $domain_id;?>
                            <?php $url['edited_domain_id'] = $usedDomainId;?>
                            <?php $url['locale_id'] = $language_id;?>
                            <?php
                            $active = "";
                            if ($locale_id == $language_id && $usedDomainId == $edited_domain_id ) {
                                $active = "active";
                            };
                            ?>
                            <?php
                            $labeArr = [];
                            $onDomains = $data['on_domains'];
                            foreach ($onDomains as $domain) {
                                $labeArr[] = '<span class="icon flag-' . $domain['country'] . ' flag" title="' . $domain['country_caption'] . '"></span>';
                            }
                            ?>
                            <li class="nav-item">
                                <?= Html::a(
                                    implode(' ', $labeArr).
                                    $data['language_caption'],
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
<?php endif;?>