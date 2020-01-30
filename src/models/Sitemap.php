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
            'entity_type_id' => Yii::t('handbook','Сущность'),
            'entity_id' => Yii::t('handbook','ИД сущности'),
            'static_file_id' => Yii::t('handbook','Статический фаил'),
            'location' => Yii::t('handbook','Адрес'),
            'section' => Yii::t('handbook','Секция'),
            'last_modified_dt' => Yii::t('handbook','Дата последнего изменения'),
            'status' => Yii::t('handbook','Статус'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'is_deleted' => Yii::t('handbook','Удалено'),
        ];
    }
}
