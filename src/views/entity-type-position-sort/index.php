<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

$this->setTitlePrefix($entitySearchModel::label());
$this->setTitle(Yii::t('yii2admin', 'Сортировка'));
$this->pushBreadcrumbs(['label' => $entitySearchModel::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(null, Yii::t('yii2admin','Добавить позицию'), null,
    [
        'class' => 'magic-modal-control',
        'data-url' => Url::to(['/handbook/entity-type-position/create', 'entity_type_id' => $entity_type_id]),
        'data-modal-size' => 'modal-lg',
        'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
    ]
);

?>
<?php Pjax::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= GridView::widget([
                'dataProvider' => $entityDataProvider,
                'searchVisible' => true,
                'searchCollapsed' => false,
                'searchParams' => [
                    'model' => $entitySearchModel,
                    'beforeContent' => $this->render('_search_extend', [
                        'positions' => $positions,
                        'entity_type_position_id' => $entity_type_position_id,
                    ]),
                ],
                'columns' => [
                    'id',
                    'name',
                    'seo_name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'dropdown' => false,
                        'template' => '{create}',
                        'buttons' => [
                            'create' => function ($url, $model) use ($type) {
                                return Html::a(
                                    '<i class="icon-checkmark3"></i>',
                                    ['create', 'id' => $model->id ],
                                    [
                                        'class' => 'admin-action list-icons-item',
                                        'title' => Yii::t('yii2admin', 'Добавить'),
                                        'data-pjax-id' => 'list-pjax',
                                        'data-pjax-url' => Url::current([], true),
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title"><?= Yii::t('yii2admin', 'Элементы'); ?></h5>
                </div>
            </div>

        </div>
    </div>

<?php Pjax::end(); ?>