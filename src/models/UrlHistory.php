<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Class UrlHistory
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UrlHistory extends ActiveRecord
{
    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'История ссылок');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{url_history}}';
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
                    'entity_type_id',
                    'entity_id',
                    'parent_id',
                ],
                'integer',
            ],
            [
                [
                    'location',
                ],
                'string',
                'max' => 255
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'entity_type_id' => Yii::t('handbook','Сущность'),
            'entity_id' => Yii::t('handbook','ИД сущности'),
            'parent_id' => Yii::t('handbook','Статический фаил'),
            'location' => Yii::t('handbook','Адрес'),
            'created_at' => Yii::t('handbook','Дата создания'),
        ];
    }
}
