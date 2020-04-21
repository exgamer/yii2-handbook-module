<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\LocalizedActiveRecord;
use concepture\yii2handbook\converters\SqlLocaleConverter;

/**
 * Post model
 *
 * @property integer $id
 * @property integer $sort
 * @property string $locale
 * @property string $locale_id
 * @property string $caption
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Locale extends LocalizedActiveRecord
{
    use StatusTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Языки');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->locale;
    }


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
                    'locale_id',
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
            'locale' => Yii::t('handbook','Код'),
            'locale_id' => Yii::t('handbook','Язык перевода'),
            'caption' => Yii::t('handbook','Наименование'),
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
        return SqlLocaleConverter::class;
    }

    /**
     * @return string
     */
    public static function uniqueField()
    {
        return 'locale_id';
    }
}
