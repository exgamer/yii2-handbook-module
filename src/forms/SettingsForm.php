<?php
namespace concepture\yii2handbook\forms;


use concepture\yii2handbook\enum\SettingsTypeEnum;
use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class SettingsForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsForm extends Form
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
