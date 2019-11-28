<?php
namespace concepture\yii2handbook\models;

use concepture\yii2handbook\enum\TagTypeEnum;
use concepture\yii2handbook\models\traits\DomainTrait;
use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2user\models\traits\UserTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;

/**
 * Class Tags
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Tags extends ActiveRecord
{
    public $allow_physical_delete = false;

    use DomainTrait;
    use UserTrait;
    use IsDeletedTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{tags}}';
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
                    'user_id',
                ],
                'integer'
            ],
            [
                [
                    'caption'
                ],
                'string',
                'max'=>100
            ],
            [
                [
                    'description'
                ],
                'string',
                'max'=>255
            ],
            [
                [
                    'caption',
                    'locale'
                ],
                'unique'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'type' => Yii::t('handbook','Тип'),
            'domain_id' => Yii::t('handbook','Домен'),
            'user_id' => Yii::t('handbook','Пользователь'),
            'locale' => Yii::t('handbook','Язык'),
            'description' => Yii::t('handbook','Описание'),
            'caption' => Yii::t('handbook','Название'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
            'is_deleted' => Yii::t('handbook','Удален'),
        ];
    }

    public function getTypeLabel()
    {
        return TagTypeEnum::label($this->type);
    }
}
