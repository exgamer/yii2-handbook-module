<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class StaticFileForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileForm extends Form
{
    public $domain_id;
    public $type;
    public $is_hidden;
    public $status = StatusEnum::ACTIVE;
    public $filename;
    public $extension;
    public $content;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'filename',
                    'extension',
                    'content',
                    'type',
                ],
                'required'
            ],
        ];
    }
}
