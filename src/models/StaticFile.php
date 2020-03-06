<?php
namespace concepture\yii2handbook\models;

use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2logic\enum\StatusEnum;
use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;

/**
 * Class StaticFile
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFile extends ActiveRecord
{
    public $allow_physical_delete = false;

    use StatusTrait;
    use IsDeletedTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Статические файлы');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->filename. "." .$this->extension;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{static_file}}';
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
                    'type',
                    'is_hidden',
                ],
                'integer',
            ],
            [
                [
                    'filename',
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
                    'filename',
                    'extension',
                    'type',
                    'domain_id',
                ],
                'unique',
                'targetAttribute' => ['filename', 'extension', 'type', 'domain_id']
            ],
            [
                [
                    'type'
                ],
                'default',
                'value' => StaticFileTypeEnum::CUSTOM
            ],
            [
                [
                    'last_modified_dt'
                ],
                'date',
                'format' => 'php:Y-m-d H:i:s'
            ],
            [
                [
                    'filename',
                    'extension'
                ],
                'filter',
                'filter'=>'strtolower'
            ],
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
            'is_deleted' => Yii::t('handbook','Удалено'),
        ];
    }

    /**
     * Возвращает метку типа
     *
     * @return string|null
     */
    public function typeLabel()
    {
        return StaticFileTypeEnum::label($this->type);
    }
}
