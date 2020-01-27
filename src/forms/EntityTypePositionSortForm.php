<?php

namespace concepture\yii2handbook\forms;

use concepture\yii2logic\enum\StatusEnum;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Сортировка сущностей по позиции в приложении
 *
 * @property int $entity_id
 * @property int $entity_type_id
 * @property int $entity_type_position_id
 * @property int $domain_id
 * @property int $sort
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortForm extends BaseForm
{
    public $entity_id;
    public $entity_type_id;
    public $entity_type_position_id;
    public $sort = 1;
    public $domain_id;

    /**
    * @inheritdoc
    */
    public function formRules()
    {
        return [
            [
                [
                    'entity_id',
                    'entity_type_position_id',
                    'entity_type_id'
                ],
                'required'
            ]
        ];
    }
}
