<?php

namespace concepture\yii2handbook\forms;

use concepture\yii2logic\enum\StatusEnum;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Форма позиций сущностей в приложении
 *
 * @property string $caption
 * @property string $alias
 * @property int $status
 * @property int $domain_id
 * @property int $entity_type_id
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionForm extends BaseForm
{
    public $caption; 
    public $alias; 
    public $status = StatusEnum::ACTIVE;
    public $domain_id;
    public $entity_type_id;

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [
                [
                    'caption',
                    'alias'
                ],
                'required'
            ]
        ];
    }
}
