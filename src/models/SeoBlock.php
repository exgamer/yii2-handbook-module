<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\traits\IsDeletedTrait;

/**
 * Модель сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlock extends \concepture\yii2logic\models\ActiveRecord
{
    use IsDeletedTrait;

    /**
     * @inheritDoc
     */
    public static function label()
    {
        return Yii::t('handbook', 'SEO блоки');
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
        return 'seo_block';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'caption',
                    'position',
                    'content'
                ],
                'required'
            ],
            [
                [
                    'position',
                    'sort',
                    'is_deleted',
                    'domain_id',
                    'status'
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
                    'caption'
                ],
                'unique',
                'targetAttribute' => ['caption', 'domain_id', 'status'],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'caption' => Yii::t('handbook', 'Название (только для админа)'),
            'position' => Yii::t('handbook', 'Позиция'),
            'sort' => Yii::t('handbook', 'Очередность'),
            'is_deleted' => Yii::t('handbook', 'Удален'),
            'content' => Yii::t('handbook', 'Контент'),
            'status' => Yii::t('handbook', 'Статус'),
        ];
    }
}