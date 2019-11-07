<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;


class SettingsForm extends Form
{
    public $domain_id;
    public $locale;
    public $name;
    public $value;

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
