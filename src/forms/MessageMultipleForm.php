<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseModel;
use concepture\yii2handbook\traits\VirtualAttributesTrait;

/**
 * Форма редактирования переводов, пачкой
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MessageMultipleForm extends BaseModel
{
    use VirtualAttributesTrait;

    /**
     * @var array
     */
    public $ids = [];

    /**
     * @var array
     */
    public $languages = [];

    /**
     * @var array
     */
    public $plurals = [];

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'ids',
                ],
                'each',
                'rule' => [
                    'integer',
                ]
            ],
            [
                [
                    'languages',
                ],
                'each',
                'rule' => [
                    'string',
                ]
            ],
            [
                [
                    'plurals',
                ],
                'safe',
            ],
        ];
    }
}