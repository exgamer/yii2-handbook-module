<?php
namespace concepture\yii2handbook\forms;

use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class PaymentSystemForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PaymentSystemForm extends Form
{
    public $caption;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'caption',
                ],
                'required'
            ],
        ];
    }
}
