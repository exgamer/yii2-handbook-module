<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;

/**
 * Class Sitemap
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Sitemap extends ActiveRecord
{
    use StatusTrait;
    use IsDeletedTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Карты саита');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->section;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{sitemap}}';
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
                    'entity_type_id',
                    'entity_id',
                    'static_file_id',
                ],
                'integer',
            ],
            [
                [
                    'location',
                    'section',
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'extension',
                ],
                'string',
                'max' => 10
            ],
            [
                [
                    'content'
                ],
                'string'
            ],
            [
                [
                    'last_modified_dt'
                ],
                'date',
                'format' => 'php:Y-m-d H:i:s'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'filename' => Yii::t('handbook','Название фаила'),
            'extension' => Yii::t('handbook','Расширение фаила'),
            'content' => Yii::t('handbook','Контент'),
            'status' => Yii::t('handbook','Статус'),
            'type' => Yii::t('handbook','Тип'),
            'is_hidden' => Yii::t('handbook','Скрытый'),
            'created_at' => Yii::t('handbook','Дата создания'),
        ];
    }
}
