<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class EntityTypeForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityTypeForm extends Form
{
    public $table_name;
    public $caption;
    public $status = StatusEnum::INACTIVE;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'table_name',
                    'caption',
                ],
                'required'
            ],
        ];
    }
}
