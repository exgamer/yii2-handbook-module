<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseModel;
use concepture\yii2handbook\traits\VirtualAttributesTrait;

/**
 * Форма редактирования SEO настроек, пачкой
 *
 * @todo возможно вынести в абстракцию
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoSettingsMultipleForm extends BaseModel
{
    use VirtualAttributesTrait;

    /**
     * @var array
     */
    public $ids = [];

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
            ]
        ];
    }
}