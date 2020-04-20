<?php

namespace concepture\yii2handbook\models;

use concepture\yii2logic\converters\Converter;
use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\LocalizedActiveRecord;

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

    /**
     * @inheritDoc
     * @return mixed|string
     */
    public static function getLocaleConverterClass()
    {
        return LocaleConverter::class;
    }

    /**
     * НЕ УБИРАТЬ
     * @return string
     */
    public static function uniqueField()
    {
        return 'locale_id';
    }
}

class LocaleConverter extends Converter
{
    public static function key($value)
    {
        $locales = Yii::$app->localeService->getCatalogBySql();
        $locales = array_flip($locales);
        if (isset($locales[$value])){
            return $locales[$value];
        }

        return $value;
    }

    public static function value($key)
    {
        $locales = Yii::$app->localeService->getCatalogBySql();
        if (isset($locales[$key])){
            return $locales[$key];
        }

        return $key;
    }
}
