<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseModel;
use concepture\yii2handbook\traits\VirtualAttributesTrait;
use concepture\yii2handbook\models\behaviors\PluralMessageBehavior;

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

    public $originText = null;
    /**
     * @var array
     */
    public $plurals = [];

    public function behaviors()
    {
        return [
            'PluralMessageBehavior' => [
                'class' => PluralMessageBehavior::class,
                'originText' => 'originText',
                'pluralAttr' => 'plurals',
            ],
        ];
    }

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
                    'originText',
                    'plurals',
                ],
                'safe',
            ],
        ];
    }
}