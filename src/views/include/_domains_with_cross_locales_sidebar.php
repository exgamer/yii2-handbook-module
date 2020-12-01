<?php
use yii\helpers\Html;

Yii::$app->getView()->viewHelper()->setSecondSidebarState(true);
$allDomainsDataOriginal = Yii::$app->domainService->getDomainsData();
$allDomainsData = \yii\helpers\ArrayHelper::index($allDomainsDataOriginal, 'alias');
$languages = Yii::$app->domainService->getLanguagesByDomains();
$domainsData = Yii::$app->domainService->getModelDomains($originModel ?? null);
if (! isset($locale_id)) {
    if (isset($domainsData[$domain_id]['language_id'])) {
        $locale_id = $domainsData[$domain_id]['language_id'];
    } else {
        $locale_id = $allDomainsDataOriginal[$domain_id]['language_id'];
    }
}
?>
<?php if(count($domainsData) > 0 && ! empty($languages)) :?>
    <div class="sidebar bg-transparent sidebar-secondary sidebar-component-left border-0 shadow-0 sidebar-expand-lg sidebar-expand-md" style="">
        <div class="sidebar-content" data-current-domain-id="<?= $domain_id;?>">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="card-title font-weight-semibold">
                        <?= Yii::t('yii2admin', 'Языки');?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs nav-tabs-vertical flex-column border-bottom-0">
                        <?php foreach ($languages as $language_id => $data) :?>
                            <?php foreach ($data as $language_domain_id => $dData) :?>
                                <?php

                                if (
                                ! \Yii::$app->user->hasDomainAccess($language_domain_id)
                                ) {
                                    continue;
                                }
                                ?>
                                <?php $url['domain_id'] = $language_domain_id;?>
                                <?php $url['edited_domain_id'] = $language_domain_id;?>
                                <?php $url['locale_id'] = $language_id;?>
                                <?php
                                $active = "";
                                if ($locale_id == $language_id && $language_domain_id == $edited_domain_id) {
                                    $active = "active";
                                };
                                ?>
                                <?php
                                $labeArr = [];
                                $onDomains = $dData['on_domains'];
                                foreach ($onDomains as $domain) {
                                    $labeArr[] = '<div class="mb-2"> <span class="icon flag-' . $domain['country'] . ' flag"></span>' . $domain['country_caption'] . "</div>" ;
                                }
                                ?>
                                <li class="nav-item">
                                    <?= Html::a(
                                        '<div class="mb-2"> ' . '<span class="icon flag-' . $dData['language_country_iso'] . ' flag"></span>'  . $dData['language_caption'] . "</div>" . " <span data-html='true' data-popup='popover' data-trigger='hover' data-original-title='" . Yii::t('yii2admin', 'Используется на версиях') . "' data-content='" . implode(' ', $labeArr)."' class='icon-question4 font-size-sm ml-auto'> </span>",
                                        $url,
                                        [
                                            'class' => "nav-link d-flex align-items-center {$active}",
                                            'data-pjax' => 0
                                        ]
                                    )
                                    ?>
                                </li>
                            <?php endforeach;?>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>
