<?php
use yii\helpers\Html;

Yii::$app->getView()->viewHelper()->setSecondSidebarState(true);
$domainsData = Yii::$app->domainService->getModelDomains($originModel ?? null);
//Запоминаем переданный $url
$passedUrl = $url;
?>
<?php if(count($domainsData) > 1) :?>
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
                            //Восстанавливаем переданный $url
                            $url = $passedUrl;
                            if (
                                (! \Yii::$app->user->hasDomainAccess($id))
                                ||
                                (isset($onlyAliases) && ! in_array($data['alias'], $onlyAliases))
                            ) {
                                continue;
                            }

                            $canEdit = Yii::$app->user->canEdit(Yii::$app->controller, $id);
                            // Если нет доступа на модификацию меняем ссылку на просмотр
                            if (! $canEdit) {
                                $url[0] = 'view';
                            }

                            ?>
                            <?php $active = ($domain_id == $id ? 'active' : "");?>
                            <?php $url['domain_id'] = $id;?>
                            <li class="nav-item">
                                <?php if ($canEdit):?>
                                    <?= Html::a(
                                        '<span class="icon flag-' . $data['country'] . ' flag"></span>'. $data['country_caption']. " (" . $data['country'] . ")",
                                        $url,
                                        [
                                            'class' => "nav-link {$active}",
                                        ]
                                    ) ?>
                                <?php else:?>
                                    <?= Html::a(
                                        '<span class="icon flag-' . $data['country'] . ' flag"></span>'. $data['country_caption']. " (" . $data['country'] . ")",
                                        '#',
                                        [
                                            'class' => ' magic-modal-control nav-link {$active}',
                                            'data-url' => \yii\helpers\Url::to($url),
                                            'data-modal-size' => 'modal-lg'
                                        ]);?>
                                <?php endif;?>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>