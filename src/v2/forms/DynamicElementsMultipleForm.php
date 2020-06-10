<?php

namespace concepture\yii2handbook\v2\forms;

use kamaelkz\yii2admin\v1\forms\BaseModel;
use concepture\yii2handbook\traits\VirtualAttributesTrait;

/**
 * Форма редактирования динамических элементов, пачкой
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsMultipleForm extends BaseModel
{
    use VirtualAttributesTrait;

    /**
     * @var array
     */
    public $ids = [];
    /**
     * @var integer
     */
    public $domain_id;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'ids'
                ],
                'each',
                'rule' => [
                    'integer'
                ]
            ],
            [
                [
                    'domain_id'
                ],
                'integer'
            ]
        ];
    }
}