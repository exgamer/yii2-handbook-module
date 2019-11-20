<?php
namespace concepture\yii2handbook\forms;


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
                ],
                'required'
            ],
        ];
    }
}
