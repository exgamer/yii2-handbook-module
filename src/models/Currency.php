<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\LocalizedActiveRecord;
use concepture\yii2handbook\converters\LocaleConverter;

/**
 * Class Currency
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Currency extends LocalizedActiveRecord
{
    use StatusTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Валюта');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{currency}}';
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
                    'locale',
                ],
                'integer'
            ],
            [
                [
                    'name'
                ],
                'string',
                'max'=>20
            ],
            [
                [
                    'code',
                    'symbol',
                    'symbol_native',
                ],
                'string',
                'max'=>10
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'status' => Yii::t('handbook','Статус'),
            'code' => Yii::t('handbook','Код'),
            'name' => Yii::t('handbook','Наименование'),
            'symbol' => Yii::t('handbook','Символ'),
            'symbol_native' => Yii::t('handbook','Родной символ'),
            'locale' => Yii::t('handbook','Язык'),
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
