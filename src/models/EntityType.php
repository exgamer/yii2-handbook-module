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
 * @property boolean $sort_module - использовать в модуле сортировки
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityType extends ActiveRecord
{
    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Сущности');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->table_name;
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
                'integer',
            ],
            [
                [
                    'sort_module'
                ],
                'boolean',
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
            'sort_module' => Yii::t('handbook','Использование сущности в разделе сортировки'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
