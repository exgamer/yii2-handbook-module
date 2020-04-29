<?php
namespace concepture\yii2handbook\forms;

use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\validators\ModelValidator;
use kamaelkz\yii2cdnuploader\pojo\CdnImagePojo;
use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class PaymentSystemForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PaymentSystemForm extends Form
{
    public $name;
    public $locale;
    public $status = StatusEnum::INACTIVE;
    public $logo;

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'locale',
                    'name',
                    'logo',
                ],
                'required'
            ],
            [
                [
                    'logo',
                ],
                ModelValidator::class,
                'modelClass' => CdnImagePojo::class,
                'modifySource' => false,
                'message' => Yii::t('common', 'Некорректный формат данных.')
            ],
        ];
    }
}
