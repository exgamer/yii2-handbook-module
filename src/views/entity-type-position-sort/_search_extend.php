<?php
    use yii\helpers\Html;
?>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <label class="control-label" for="entity_type_id">
            <?= Yii::t('yii2admin', 'Позиция');?>
        </label>
        <div class="form-group">
            <?= Html::dropDownList(
                'entity_type_position_id',
                $entity_type_position_id,
                $positions ??  [],
                [
                    'id' => 'entity_type_position_id',
                    'class' => 'form-control form-control-uniform active-form-refresh-control',
                    'prompt' => ''
                ]
            )?>
        </div>
    </div>
</div>