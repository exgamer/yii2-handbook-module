<?php

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

if(! $autoRender) {
    $this->setTitle(Yii::t('yii2admin', 'Управление элементами страницы'));
    $this->pushBreadcrumbs($this->title);
}

$dynamic_elements_count = $dynamicElementsDataProvider->getTotalCount();
$translation_count = $sourceMessageDataProvider->getTotalCount();
?>
<?php if(! $autoRender) :?>
    <?php Pjax::begin(); ?>
<?php endif;?>
    <?php if($autoRender) :?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <?= Yii::t('yii2admin', 'Другие элементы на странице')?>
                </h5>
            </div>
        </div>
    <?php endif;?>
    <div class="tab-content">
        <div class="tab-pane fade active show">
    <!--        --><?php //if ($translation_count > 0) : ?>
                <ul class="mb-3 nav nav-tabs nav-tabs-solid nav-justified bg-light">
                    <li class="nav-item">
                        <?= Html::a(
                            Yii::t('yii2admin', 'Динамические элементы') . " ($dynamic_elements_count)",
                            \yii\helpers\Url::current(['manage_tab' => 'de']),
                            [ 'class' => 'nav-link ' . ($manage_tab === 'de' ? 'active' : null) ]
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= Html::a(
                            Yii::t('yii2admin', 'Переводы')  . " ($translation_count)",
                            \yii\helpers\Url::current(['manage_tab' => 'tr']),
                            [ 'class' => 'nav-link ' . ($manage_tab === 'tr' ? 'active' : null) ]
                        ) ?>
                    </li>
                </ul>
    <!--        --><?php //endif;?>
    <!--        --><?php //if($translation_count > 0) :?>
            <div class="tab-content">
                    <div class="tab-pane fade <?= $manage_tab === 'de' ? 'active' : null;?> show" id="de_tab_content">
    <!--                --><?php //endif;?>
                    <?= $this->render('tabs/dynamic_elements', [
                        'searchModel' => $dynamicElementSearch,
                        'dataProvider' => $dynamicElementsDataProvider,
                    ]);?>
    <!--                --><?php //if($translation_count > 0) :?>
                    </div>
                    <div class="tab-pane fade <?= $manage_tab === 'tr' ? 'active' : null;?> show" id="tr_tab_content">
                        <?= $this->render('tabs/translation', [
                            'searchModel' => $sourceMessageSearch,
                            'dataProvider' => $sourceMessageDataProvider,
                        ]);?>
                    </div>
    <!--                --><?php //endif;?>
            </div>
        </div>
    </div>
<?php if(! $autoRender) :?>
    <?php Pjax::end(); ?>
<?php endif;?>

