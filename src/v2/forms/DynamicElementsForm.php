<?php

namespace concepture\yii2handbook\v2\forms;

use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\forms\BaseForm;
use concepture\yii2handbook\models\behaviors\PluralMessageBehavior;

/**
 * Форма динамических элементов версия 2
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsForm extends BaseForm
{
    public $domain_id;
    public $route;
    public $route_hash;
    public $route_params;
    public $route_params_hash;
    public $name;
    public $caption;
    public $type = SettingsTypeEnum::TEXT;
    public $general = false;
    public $multi_domain = true;
    public $value;
    public $value_params;
    public $unique_params;
    public $unique_params_hash;
    public $hint;
    public $originValue;
    public $plurals = [];

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'PluralMessageBehavior' => [
                'class' => PluralMessageBehavior::class,
                'originText' => 'originValue',
                'pluralAttr' => 'plurals',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'name',
                    'caption',
                    'type'
                ],
                'required'
            ],
            [
                [
                    'originValue',
                    'plurals',
                ],
                'safe',
            ],
        ];
    }
}
