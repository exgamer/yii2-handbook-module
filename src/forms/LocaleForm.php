<?php
namespace concepture\yii2locale\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class LocaleForm
 * @package concepture\yii2locale\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleForm extends Form
{
    public $sort;
    public $status;
    public $locale;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'locale',
                ],
                'required'
            ],
        ];
    }
}
