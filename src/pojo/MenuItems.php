<?php

namespace concepture\yii2handbook\pojo;

use concepture\yii2logic\pojo\Pojo;

/**
 * Class MenuItems
 *
 * @package concepture\yii2handbook\pojo
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuItems extends Pojo
{
    public $text;
    public $url;
    public $icon;
    public $isNewRecord = true;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'text',
                    'url',
                ],
                'required',
            ],
            [
                [
                    'text',
                    'url',
                    'icon',
                ],
                'string',
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'text' => \Yii::t('handbook', 'Текст'),
            'url' => \Yii::t('handbook', 'Ссылка'),
            'icon' => \Yii::t('handbook', 'Иконка'),
        ];
    }
}