<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class LocaleForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleForm extends Form
{
    public $sort;
    public $status;
    public $locale;
    public $caption;
    public $locale_id;
    public $declination_format;

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
                    'locale_id',
                    'declination_format',
                ],
                'required'
            ],
        ];
    }
}
