<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2handbook\models\traits\DomainTrait;

/**
 * Class Menu
 *
 * @property string $id
 * @property string $type
 * @property string $caption
 * @property string $url
 * @property string $domain_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $is_deleted
 * @property string $sort
 *
 * @package concepture\yii2handbook\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class Menu extends ActiveRecord
{
    use DomainTrait;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{menu}}';
    }

    /**
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Меню');
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->caption;
    }


    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'type',
                    'domain_id',
                    'status',
                    'is_deleted',
                ],
                'integer'
            ],
            [
                [
                    'caption'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'url',
                ],
                'string',
                'max' => 1024
            ],
            [
                [
                    'type',
                    'domain_id',
                ],
                'unique',
                'targetAttribute' => ['type', 'domain_id'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'type' => Yii::t('handbook','Тип'),
            'caption' => Yii::t('handbook','Наименование'),
            'url' => Yii::t('handbook','Ссылка'),
            'domain_id' => Yii::t('handbook','Домен'),
            'status' => Yii::t('handbook','Статус'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'is_deleted' => Yii::t('handbook','Удален'),
            'sort' => Yii::t('handbook', 'Сортировка'),
        ];
    }
}
