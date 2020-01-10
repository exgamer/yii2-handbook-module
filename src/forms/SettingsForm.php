<?php

namespace concepture\yii2handbook\forms;

use concepture\yii2handbook\enum\SettingsTypeEnum;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Class SettingsForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsForm extends BaseForm
{
    public $domain_id;
    public $locale;
    public $type = SettingsTypeEnum::TEXT;
    public $name;
    public $value;
    public $description;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'name',
                    'value',
                    'type',
                ],
                'required'
            ],
        ];
    }
}
