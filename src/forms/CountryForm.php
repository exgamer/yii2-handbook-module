<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class CountryForm
 * @package concepture\yii2handbook\forms
 */
class CountryForm extends Form
{
    public $sort;
    public $status;
    public $locale;
    public $iso;
    public $caption;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'locale',
                    'caption',
                    'iso',
                ],
                'required'
            ],
        ];
    }
}
