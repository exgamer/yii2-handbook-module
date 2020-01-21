<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\TranslitValidator;
use concepture\yii2handbook\models\EntityType;
use concepture\yii2logic\models\traits\StatusTrait;

/**
 * Позиции сущностей в приложении
 *
 * @property int $id
 * @property string $caption
 * @property string $alias
 * @property int $status
 * @property int $domain_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $entity_type_id
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePosition extends ActiveRecord
{
    use StatusTrait;

    /**
    * @see \concepture\yii2logic\models\ActiveRecord:label()
    *
    * @return string
    */
    public static function label()
    {
        return Yii::t('common', 'Позиции сущностей');
    }

    /**
    * @see \concepture\yii2logic\models\ActiveRecord:toString()
    * @return string
    */
    public function toString()
    {
        return $this->id;
    }

    /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return '{{entity_type_position}}';
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
                    'domain_id',
                    'entity_type_id'
                ],
                'integer'
            ],
            [
                [
                    'caption',
                    'alias'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'alias',
                ],
                TranslitValidator::class,
                'source' => 'caption'
            ],
            [
                [
                    'alias',
                    'domain_id'
                ],
                'unique',
                'targetAttribute' => ['alias', 'domain_id']
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntityType()
    {
        return $this->hasOne(EntityType::class, ['id' => 'entity_type_id']);
    }

    /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook', '#'),
            'caption' => Yii::t('handbook', 'Название'),
            'alias' => Yii::t('handbook', 'Альяс'),
            'status' => Yii::t('handbook', 'Статус'),
            'entity_type_id' => Yii::t('handbook', 'Сущность'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
