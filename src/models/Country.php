<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\LocalizedActiveRecord;
use concepture\yii2handbook\converters\LocaleConverter;

/**
 * Country model
 *
 * @property integer $id
 * @property integer $sort
 * @property string $locale
 * @property string $caption
 * @property string $iso
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Country extends LocalizedActiveRecord
{
    use StatusTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Страны');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->iso;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{country}}';
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
                    'locale',
                ],
                'integer'
            ],
            [
                [
                    'iso'
                ],
                'string',
                'max'=>2
            ],
            [
                [
                    'image',
                    'image_anons',
                ],
                'string',
                'max'=>1024
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
                    'iso'
                ],
                'unique'
            ]
        ];
    }

    /**
     * @todo костыль так как поле называется locale, в будущем избавиться - переименовать в locale_id
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Locale::class, ['id' => 'locale']);
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'sort' => Yii::t('handbook','Позиция сортировки'),
            'status' => Yii::t('handbook','Статус'),
            'locale' => Yii::t('handbook','Язык'),
            'iso' => Yii::t('handbook','ISO код страны'),
            'caption' => Yii::t('handbook','Название'),
            'image' => Yii::t('handbook','Флаг'),
            'image_anons' => Yii::t('handbook','Флаг (маленький)'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }

    /**
     * @inheritDoc
     * @return mixed|string
     */
    public static function getLocaleConverterClass()
    {
        return LocaleConverter::class;
    }
}
