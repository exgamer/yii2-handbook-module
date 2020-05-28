<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2handbook\pojo\MenuItems;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;
use concepture\yii2handbook\models\traits\DomainTrait;
use concepture\yii2logic\models\behaviors\JsonFieldsBehavior;

/**
 * Class Menu
 *
 * @property string $id
 * @property string $type
 * @property string $caption
 * @property string $url
 * @property string $desktop_max_count
 * @property string $link_all_caption
 * @property string $link_all_url
 * @property string $items
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
    use StatusTrait;
    use DomainTrait;
    use IsDeletedTrait;

    /** @var bool */
    public $allow_physical_delete = false;

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
    public function behaviors()
    {
        return [
            'JsonFieldsBehavior' => [
                'class' => JsonFieldsBehavior::class,
                'jsonAttr' => [
                    'items' => [
                        'class' => MenuItems::class,
                    ],
                ],
            ],
        ];
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
                    'desktop_max_count',
                    'domain_id',
                    'status',
                    'is_deleted',
                ],
                'integer'
            ],
            [
                [
                    'caption',
                    'link_all_caption',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'url',
                    'link_all_url',
                ],
                'string',
                'max' => 1024,
            ],
            [
                [
                    'items',
                ],
                'safe',
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
            'desktop_max_count' => Yii::t('handbook','Количество пунктов на десктопе'),
            'link_all_caption' => Yii::t('handbook','Наименование ссылки на все пункты'),
            'link_all_url' => Yii::t('handbook','Ссылка на все пункты'),
            'items' => Yii::t('handbook','Пункты меню'),
            'domain_id' => Yii::t('handbook','Домен'),
            'status' => Yii::t('handbook','Статус'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'is_deleted' => Yii::t('handbook','Удален'),
            'sort' => Yii::t('handbook', 'Сортировка'),
        ];
    }
}
