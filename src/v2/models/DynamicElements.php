<?php

namespace concepture\yii2handbook\v2\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\MD5Validator;
use concepture\yii2handbook\models\traits\DomainTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;

/**
 * Динамические элементы версия 2
 *
 * @property integer $id
 * @property string $route
 * @property string $route_hash
 * @property string $route_params
 * @property string $route_params_hash
 * @property string $name
 * @property string $caption
 * @property integer $type
 * @property boolean $general
 * @property boolean $multi_domain
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_deleted
 * @property integer $sort
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElements extends ActiveRecord
{
    use DomainTrait,
        HasDomainPropertyTrait;

    /**
     * @inheritDoc
     */
    public static function label()
    {
        return Yii::t('handbook', 'Динамические элементы');
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'dynamic_elements_v2';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'type',
                ],
                'integer'
            ],
            [
                [
                    'general',
                    'multi_domain',
                ],
                'boolean'
            ],
            [
                [
                    'name',
                    'caption'
                ],
                'string',
                'max' => 512
            ],
            [
                [
                    'value',
                ],
                'string',
                'max' => 512
            ],
            [
                [
                    'route',
                    'route_params'
                ],
                'string',
                'max'=>1024
            ],
            [
                [
                    'route_hash',
                ],
                MD5Validator::class,
                'source' => 'route'
            ],
            [
                [
                    'route_params_hash',
                ],
                MD5Validator::class,
                'source' => 'route_params',
                'skipOnEmpty' => false
            ],
            [
                [
                    'route_hash',
                    'route_params_hash',
                ],
                'string',
                'max' => 32
            ],
            [
                [
                    'route_hash'
                ],
                'unique',
                'targetAttribute' => ['route_hash', 'route_params_hash', 'name']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'route' => Yii::t('handbook','Роут'),
            'route_params' => Yii::t('handbook','Параметры роута'),
            'route_hash' => Yii::t('handbook','Хэш роута'),
            'name' => Yii::t('handbook','Ключ'),
            'caption' => Yii::t('handbook','Наименование'),
            'value' => Yii::t('handbook','Значение'),
            'type' => Yii::t('handbook','Тип'),
            'general' => Yii::t('handbook','Общий'),
            'multi_domain' => Yii::t('handbook','Мульти доменный'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'sort' => Yii::t('handbook','Позиция сортировки'),
        ];
    }
}
