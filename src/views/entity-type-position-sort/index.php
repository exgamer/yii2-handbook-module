<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\lists\grid\EditableColumn;
use kamaelkz\yii2admin\v1\enum\FlashAlertEnum;
use kamaelkz\yii2admin\v1\widgets\notifications\alert\Alert;

$this->setTitlePrefix($entitySearchModel::label());
$this->setTitle(Yii::t('yii2admin', 'Сортировка'));
$this->pushBreadcrumbs(['label' => $entitySearchModel::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);
$this->viewHelper()->pushPageHeader(['index'], $entitySearchModel::label(),'icon-list');
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
                'columns' => ArrayHelper::merge(
                        $entityColumns,
                        [
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'dropdown' => false,
                                'template' => '{create}',
                                'buttons' => [
                                    'create' => function ($url, $model) use ($entity_type_id, $entity_type_position_id) {
                                        if(! $entity_type_position_id) {
                                            return null;
                                        }

                                        return Html::a(
                                            '<i class="icon-checkmark3"></i>',
                                            [
                                                '/handbook/entity-type-position-sort/create',
                                                'entity_id' => $model->id,
                                                'entity_type_id' => $entity_type_id,
                                                'entity_type_position_id' => $entity_type_position_id
                                            ],
                                            [
                                                'class' => 'admin-action list-icons-item',
                                                'title' => Yii::t('yii2admin', 'Добавить'),
                                                'data-pjax-id' => 'list-pjax',
                                                'data-pjax-url' => Url::current([], true),
                                            ]
                                        );
                                    },
                                ],
                            ]
                        ]
                    )
            ]); ?>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">
                        <?= Yii::t('yii2admin', 'Элементы'); ?>
                    </h5>
                </div>
                <?php if(empty($entity_type_position_id)):?>
                    <div class="card-body">
                        <?= Alert::widget([
                            'type' => FlashAlertEnum::INFO,
                            'message' => Yii::t('yii2admin', 'Необходимо выбрать позицию сущности.'),
                        ]) ;?>
                    </div>
                <?php endif;?>
            </div>
            <?php if(! empty($entity_type_position_id)):?>
                <?= GridView::widget([
                    'dataProvider' => $sortDataProvider,
                    'searchVisible' => false,
                    'dragAndDrop' => true,
                    'columns' => [
                        [
                            'attribute' => 'entity_id',
                            'value' => function($data) {
                                return $data['entity_id'];
                            },
                            'label' => '#',
                        ],
                        [
                            'attribute' => 'label',
                            'value' => function($data) {
                                return $data['label'];
                            },
                            'label' => Yii::t('yii2admin', 'Сущность'),
                        ],
                                [
                                    'attribute' => 'sort',
                                    'class' => EditableColumn::class,
                                    'contentOptions' => [
                                        'style' => 'width:15%',
                                        'class'=> 'text-center'
                                    ],
                                    'label' => Yii::t('yii2admin', 'Сортировка'),
                                ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'dropdown' => false,
                            'template' => '{delete}',
                            'buttons' => [
                                'delete' => function ($url, $data) {
                                    return Html::a(
                                        '<i class="icon-cross2"></i>',
                                        ['/handbook/entity-type-position-sort/delete', 'id' => $data['id']],
                                        [
                                            'class' => 'admin-action list-icons-item',
                                            'title' => Yii::t('yii2admin', 'Удалить'),
                                            'data-pjax-id' => 'list-pjax',
                                            'data-pjax-url' => Url::current([], true),
                                            'data-swal' => Yii::t('yii2admin' , 'Удалить'),
                                        ]
                                    );
                                },
                            ],
                        ]
                    ]
                ]); ?>
            <?php endif;?>
        </div>
    </div>

<?php Pjax::end(); ?>