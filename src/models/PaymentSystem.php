<?php

namespace concepture\yii2handbook\models;

use Yii;
use kamaelkz\yii2cdnuploader\traits\ModelTrait;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;
use concepture\yii2logic\models\LocalizedActiveRecord;
use concepture\yii2handbook\converters\LocaleConverter;

/**
 * Class PaymentSystem
 *
 * Платежные системы
 *
 * @package concepture\yii2handbook\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 *
 * @property integer $id
 * @property string $logo
 * @property string $name
 * @property string $locale
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_deleted
 * @property integer $sort
 */
class PaymentSystem extends LocalizedActiveRecord
{
    use StatusTrait;
    use IsDeletedTrait;
    use ModelTrait;

    /** @var bool */
    public $allow_physical_delete = false;

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'payment_system';
    }

    /**
     * @inheritDoc
     */
    public static function label()
    {
        return Yii::t('common', 'Платежные системы');
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return  [
            [
                [
                    'status',
                    'locale',
                    'sort',
                ],
                'integer'
            ],
            [
                [
                    'name',
                    'logo',
                ],
                'string',
                'max' => 1024
            ],
            [
                [
                    'name',
                    'logo',
                ],
                'trim'
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
            'status' => Yii::t('common', 'Статус'),
            'locale' => Yii::t('common', 'Язык'),
            'logo' => Yii::t('common', 'Логотип'),
            'name' => Yii::t('common', 'Наименование'),
            'created_at' => Yii::t('common', 'Дата создания'),
            'updated_at' => Yii::t('common', 'Дата обновления'),
            'is_deleted' => Yii::t('common', 'Удален'),
            'sort' => Yii::t('common', 'Позиция сортировки'),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getLocaleConverterClass()
    {
        return LocaleConverter::class;
    }
}