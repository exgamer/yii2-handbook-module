<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\TranslitValidator;
use concepture\yii2handbook\models\EntityType;
use concepture\yii2logic\models\traits\StatusTrait;

/**
 * Сортировка сущностей по позиции в приложении
 *
 * @property int $id
 * @property int $entity_id
 * @property int $entity_type_position_id
 * @property int $domain_id
 * @property int $sort
 * @property string $created_at
 * @property string $updated_at
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSort extends ActiveRecord
{
    /**
    * @see \concepture\yii2logic\models\ActiveRecord:label()
    *
    * @return string
    */
    public static function label()
    {
        return Yii::t('common', 'Сортировка сущностей по позиции');
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
        return '{{entity_type_position_sort}}';
    }

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [
            [
                [
                    'entity_id',
                    'entity_type_position_id',
                    'domain_id',
                    'sort',
                ],
                'integer'
            ],
        ];
    }

    /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook', '#'),
            'entity_id' => Yii::t('handbook', 'Сущность'),
            'entity_type_position_id' => Yii::t('handbook', 'Позиция сущности'),
            'sort' => Yii::t('handbook', 'Сортировка'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
