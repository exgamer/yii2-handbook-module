<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class CurrencyForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CurrencyForm extends Form
{
    public $status;
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
                    'iso',
                    'caption',
                ],
                'required'
            ],
        ];
    }
}
