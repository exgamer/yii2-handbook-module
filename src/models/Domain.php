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
 * @property string $domain
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
            ['domain', 'url', 'defaultScheme' => 'https']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('domain','#'),
            'sort' => Yii::t('domain','Позиция сортировки'),
            'status' => Yii::t('domain','Статус'),
            'domain' => Yii::t('domain','Домен'),
            'caption' => Yii::t('domain','Название'),
            'alias' => Yii::t('domain','Альяс'),
            'description' => Yii::t('domain','Описание'),
            'created_at' => Yii::t('domain','Дата создания'),
            'updated_at' => Yii::t('domain','Дата обновления'),
        ];
    }
}
