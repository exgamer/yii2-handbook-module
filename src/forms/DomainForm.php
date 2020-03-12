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
    public $caption;
    public $alias;
    public $description;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'caption',
                    'alias',
                ],
                'required'
            ],
        ];
    }
}
