<?php
namespace concepture\yii2handbook\models;

use concepture\yii2logic\validators\TranslitValidator;
use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Domain model
 *
 * @property integer $id
 * @property integer $sort
 * @property string $caption
 * @property string $alias
 * @property string $description
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Domain extends ActiveRecord
{
    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Домены');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{domain}}';
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
                    'country_id'
                ],
                'integer'
            ],
            [
                [
                    'description'
                ],
                'string',
                'max'=>1024
            ],
            [
                [
                    'caption',
                    'alias',
                ],
                'string',
                'max'=>100
            ],
            [
                [
                    'alias',
                ],
                TranslitValidator::className(),
                'source' => 'caption'
            ],
            [
                [
                    'alias',
                ],
                'unique'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('domain','#'),
            'sort' => Yii::t('domain','Позиция сортировки'),
            'status' => Yii::t('domain','Статус'),
            'caption' => Yii::t('domain','Название'),
            'alias' => Yii::t('domain','Альяс'),
            'description' => Yii::t('domain','Описание'),
            'created_at' => Yii::t('domain','Дата создания'),
            'updated_at' => Yii::t('domain','Дата обновления'),
        ];
    }
}
