<?php

namespace concepture\yii2handbook\forms;

use Yii;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Class CountryForm
 * @package concepture\yii2handbook\forms
 */
class CountryForm extends BaseForm
{
    public $sort;
    public $status;
    public $locale;
    public $domain_id;
    public $iso;
    public $caption;
    public $image;
    public $image_anons;

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
