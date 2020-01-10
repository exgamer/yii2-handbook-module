<?php

namespace concepture\yii2handbook\forms;

use yii\validators\UrlValidator;
use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Форма SEO настроек
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsForm extends BaseForm
{
    public $domain_id;
    public $locale;
    public $url;
    public $url_md5_hash;
    public $name;
    public $value;
    public $caption;
    public $type = SettingsTypeEnum::TEXT;

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'url',
                    'name',
                    'value',
                    'caption',
                    'type'
                ],
                'required'
            ],
            [
                [
                    'url',
                ],
                UrlValidator::class,
                'pattern' => '/^\/(([A-Z0-9][A-Z0-9_-]*)/i'
            ],
        ];
    }
}
