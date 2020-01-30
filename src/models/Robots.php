<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2handbook\models\traits\DomainTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;

/**
 * @deprecated static file type robots use
 *
 * Индексный файл - robots.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Robots extends ActiveRecord
{
    use StatusTrait;
    use DomainTrait;
    use IsDeletedTrait;

    /**
     * @var bool
     */
    public $allow_physical_delete = false;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('static', 'Индексные файлы');
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
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{robots}}';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'status',
                    'domain_id',
                ],
                'integer'
            ],
            [
                [
                    'content'
                ],
                'string'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'domain_id' => Yii::t('handbook','Домен'),
            'status' => Yii::t('handbook','Статус'),
            'content' => Yii::t('handbook','Содержимое'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'is_deleted' => Yii::t('handbook','Удален'),
        ];
    }
}
