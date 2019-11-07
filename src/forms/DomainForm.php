<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class DomainForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainForm extends Form
{
    public $sort;
    public $status;
    public $domain;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'domain',
                ],
                'required'
            ],
        ];
    }
}
