<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Settings model
 *
 * @property integer $id
 * @property integer $domain_id
 * @property string $locale
 * @property integer $name
 * @property integer $value
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Settings extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'domain_id',
                    'locale',
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'value',
                ],
                'string'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'domain_id' => Yii::t('handbook','Домен'),
            'locale' => Yii::t('handbook','Язык'),
            'name' => Yii::t('handbook','Название'),
            'value' => Yii::t('handbook','Значение'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
