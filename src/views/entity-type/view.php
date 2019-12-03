<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use yii\helpers\Url;

$this->setTitle(Yii::t('yii2admin', 'Просмотр'));
$this->pushBreadcrumbs(['label' => Yii::t('yii2admin', 'Сущности'), 'url' => ['index']]);
$this->pushBreadcrumbs($this->title);

$this->viewHelper()->pushPageHeader();
$this->viewHelper()->pushPageHeader(['update' ,'id' => $model->id], Yii::t('yii2admin','Редактировать'), 'icon-pencil6');
$this->viewHelper()->pushPageHeader(['index'], Yii::t('yii2admin', 'Сущности'),'icon-list');
?>

<?php Pjax::begin();?>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-lg-9 col-md-8 col-sm-12">
                <h5 class="card-title">
                    <?= $model->getRoleLabel();?>
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
                                'class' => 'admin-action dropdown-item',
                                'data-swal' => Yii::t('yii2admin' , 'Редактирование'),
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
                'table_name',
                'caption',
                'created_at',
                'updated_at',
            ],
        ]) ?>
    </div>
</div>
<?php Pjax::end(); ?>

