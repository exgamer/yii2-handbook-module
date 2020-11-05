<?php
use yii\helpers\Html;

Yii::$app->getView()->viewHelper()->setSecondSidebarState(true);
$domainsData = Yii::$app->domainService->getModelDomains($originModel ?? null);
if (! isset($locale_id)) {
    $locale_id = $domainsData[$domain_id]['language_id'];
}

$languages = Yii::$app->domainService->getDomainsByLocales();
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
                        <?php foreach ($domainsData as $id => $data) :?>
                            <?php

                            if (
                                ! \Yii::$app->user->hasDomainAccess($id)
                                || (isset($onlyAliases) && ! in_array($data['alias'], $onlyAliases))
                            ) {
                                continue;
                            }
                            ?>
                            <?php $active = ($domain_id == $id ? 'active' : "");?>
                            <?php $url['domain_id'] = $id;?>

                            <?php $disabled = false;?>
                            <?php if (isset($data['languages']) && ! empty($data['languages'])) : ?>
                                <?php $disabled = true;?>
                            <?php endif;?>

                            <li class="nav-item">
                                <?= Html::a(
                                    '<span class="icon flag-' . $data['country'] . ' flag"></span>'. $data['country_caption']. " (" . $data['country'] . ")",
                                    $url,
                                    [
                                        'class' => "nav-link {$disabled}",
                                    ]
                                ) ?>
                            </li>

                            <?php
                            $langs = Yii::$app->localeService->getAllByCondition(function (\concepture\yii2logic\db\ActiveQuery $query) use ($data) {
                                $iso = $data['languages'] ?? [];
                                $query->andWhere(['locale' => $iso]);
                                $query->orderBy('sort ASC, id ASC');
                            });
                            ?>

                            <?php foreach ($langs as $lang) :?>
                                <?php $url['locale_id'] = $lang['id'];?>
                                <?php
                                $active = "";
                                if ($locale_id == $lang['id'] && $id == $domain_id ) {
                                    $active = "active";
                                }

                                ?>

                                <li class="nav-item">
                                    <?= Html::a(
                                        $lang['caption'],
                                        $url,
                                        [
                                            'class' => "nav-link {$active}",
                                        ]
                                    ) ?>
                                </li>

                            <?php endforeach;?>



                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>