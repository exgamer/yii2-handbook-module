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
    public $code;
    public $name;
    public $symbol;
    public $symbol_native;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'code',
                    'name',
                    'symbol',
                    'symbol_native',
                ],
                'required'
            ],
        ];
    }
}
