<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class TagsForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsForm extends Form
{
    public $domain_id;
    public $user_id;
    public $caption;
    public $type;
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
                    'type'
                ],
                'required'
            ],
        ];
    }
}
