<?php
namespace concepture\yii2handbook\models;

use concepture\yii2handbook\enum\SettingsTypeEnum;
use concepture\yii2handbook\models\traits\DomainTrait;
use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Settings model
 *
 * @property integer $id
 * @property integer $domain_id
 * @property string $locale
 * @property integer $name
 * @property integer $value
 * @property integer $type
 * @property integer $description
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Settings extends ActiveRecord
{
    use DomainTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Настройки');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{settings}}';
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
            ['type', 'in', 'range' => SettingsTypeEnum::all()],
            [
                [
                    'name'
                ],
                'string',
                'max'=>100
            ],
            [
                [
                    'value',
                ],
                'string'
            ],
            [
                [
                    'description'
                ],
                'string',
                'max'=>1024
            ],
            [
                [
                    'domain_id',
                    'locale',
                    'name'
                ],
                'unique',
                'targetAttribute' => ['domain_id', 'locale', 'name']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'domain_id' => Yii::t('handbook','Домен'),
            'locale' => Yii::t('handbook','Язык'),
            'description' => Yii::t('handbook','Описание'),
            'type' => Yii::t('handbook','Тип'),
            'name' => Yii::t('handbook','Название'),
            'value' => Yii::t('handbook','Значение'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
