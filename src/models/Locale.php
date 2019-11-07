<?php
namespace concepture\yii2locale\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Post model
 *
 * @property integer $id
 * @property integer $sort
 * @property string $locale
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Locale extends ActiveRecord
{
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
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('locale','#'),
            'sort' => Yii::t('locale','Позиция сортировки'),
            'status' => Yii::t('locale','Статус'),
            'locale' => Yii::t('locale','Язык'),
            'created_at' => Yii::t('locale','Дата создания'),
            'updated_at' => Yii::t('locale','Дата обновления'),
        ];
    }
}
