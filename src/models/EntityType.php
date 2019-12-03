<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * EntityType model
 *
 * @property integer $id
 * @property string $table_name
 * @property string $caption
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityType extends ActiveRecord
{
    public static function label()
    {
        return Yii::t('handbook', 'Сущности');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{entity_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'status'
                ],
                'integer'
            ],
            [
                [
                    'table_name',
                    'caption',
                ],
                'string'
            ],
            [
                [
                    'table_name'
                ],
                'unique'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'table_name' => Yii::t('handbook','Наименование таблицы'),
            'status' => Yii::t('handbook','Статус'),
            'caption' => Yii::t('handbook','Метка'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
