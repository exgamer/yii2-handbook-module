<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2handbook\enum\SeoBlockPositionEnum;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;

$this->setTitle(Yii::t('yii2admin', 'Просмотр'));
$this->pushBreadcrumbs(['label' => $model::label(), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);

$this->viewHelper()->pushPageHeader();
$this->viewHelper()->pushPageHeader(
    ['update' ,'id' => $model->id],
    Yii::t('yii2admin','Редактирование'),
    'icon-pencil6'
);
$this->viewHelper()->pushPageHeader(['index'], $model::label(),'icon-list');
?>

<?php Pjax::begin();?>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-9 col-md-8 col-sm-12">
                    <h5 class="card-title">
                        <?= $model->toString();?>
                    </h5>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-12 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-labeled btn-labeled-left dropdown-toggle" data-toggle="dropdown">
                            <b>
                                <i class="icon-cog5"></i>
                            </b>
                            <?= Yii::t('yii2admin', 'Операции');?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?= Html::a(
                                '<i class="icon-pencil6"></i>' . Yii::t('yii2admin', 'Редактирование'),
                                ['update', 'id' => $model->id],
                                [
                                    'class' => 'dropdown-item magic-modal-control',
                                    'data-url' => Url::to(['update', 'id' => $model->id]),
                                    'data-modal-size' => 'modal-lg',
                                    'data-callback' => 'function(){callbackHelper.reloadPjax("#list-pjax")}'
                                ]
                            );?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'caption',
                    [
                        'attribute' => 'position',
                        'value' => function($data){
                            return SeoBlockPositionEnum::labels()[$data->position] ?? null;
                        },
                    ],
                    'sort',
                    [
                        'attribute'=>'status',
                        'value' => function($data) {
                            return StatusEnum::labels()[$data->status] ?? null;
                        }
                    ],
                    [
                        'attribute'=>'is_deleted',
                        'filter'=> IsDeletedEnum::arrayList(),
                        'value' => function($data) {
                            return $data->isDeletedLabel();
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>
<?php Pjax::end(); ?>