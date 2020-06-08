<?php

namespace concepture\yii2handbook\v2\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\MD5Validator;
use concepture\yii2handbook\models\traits\DomainTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;

/**
 * Динамические элементы
 *
 * @property integer $id
 * @property integer $domain_id
 * @property integer $locale
 * @property string $url
 * @property string $url_md5_hash
 * @property string $name
 * @property string $value
 * @property string $caption
 * @property integer $type
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_general
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElements extends ActiveRecord
{
    use DomainTrait,
        HasDomainPropertyTrait;

    /**
     * @var integer
     */
    public $hash_count;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Динамические элементы');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     *
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
        return 'dynamic_elements_v2';
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
                    'type',
                ],
                'integer'
            ],
            [
                [
                    'is_general'
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
            ],
            [
                [
                    'url',
                ],
                'string',
                'max'=>256
            ],
            [
                [
                    'url_md5_hash',
                ],
                MD5Validator::class,
                'source' => 'url'
            ],
            [
                [
                    'url_md5_hash'
                ],
                'unique',
                'targetAttribute' => ['url_md5_hash', 'name', 'domain_id']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'domain_id' => Yii::t('handbook','Домен'),
            'locale' => Yii::t('handbook','Язык'),
            'url' => Yii::t('handbook','Адрес страницы'),
            'url_md5_hash' => Yii::t('handbook','md5 url страницы'),
            'name' => Yii::t('handbook','Ключ'),
            'value' => Yii::t('handbook','Значение'),
            'caption' => Yii::t('handbook','Наименование'),
            'type' => Yii::t('handbook','Тип'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'hash_count' => Yii::t('handbook','Количество'),
        ];
    }
}
