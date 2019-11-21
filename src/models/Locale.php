<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;

/**
 * Post model
 *
 * @property integer $id
 * @property integer $sort
 * @property string $locale
 * @property string $caption
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Locale extends ActiveRecord
{
    use StatusTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{locale}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'status',
                    'sort',
                ],
                'integer'
            ],
            [
                [
                    'locale'
                ],
                'string',
                'max'=>2
            ],
            [
                [
                    'caption'
                ],
                'string',
                'max'=>100
            ],
            [
                [
                    'locale'
                ],
                'unique'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'sort' => Yii::t('handbook','Позиция сортировки'),
            'status' => Yii::t('handbook','Статус'),
            'locale' => Yii::t('handbook','Язык'),
            'caption' => Yii::t('handbook','Метка'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
