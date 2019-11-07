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
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('locale','#'),
            'table_name' => Yii::t('locale','Наименование таблицы'),
            'status' => Yii::t('locale','Статус'),
            'caption' => Yii::t('locale','Метка'),
            'created_at' => Yii::t('locale','Дата создания'),
            'updated_at' => Yii::t('locale','Дата обновления'),
        ];
    }
}
