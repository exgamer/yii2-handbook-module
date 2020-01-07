<?php
namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\StatusTrait;


class PaymentSystem extends ActiveRecord
{
    use StatusTrait;

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:label()
     *
     * @return string
     */
    public static function label()
    {
        return Yii::t('handbook', 'Системы оплаты');
    }

    /**
     * @see \concepture\yii2logic\models\ActiveRecord:toString()
     * @return string
     */
    public function toString()
    {
        return $this->caption;
    }


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{payment_system}}';
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
                ],
                'integer'
            ],
            [
                [
                    'caption'
                ],
                'string',
                'max'=>20
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'status' => Yii::t('handbook','Статус'),

            'caption' => Yii::t('handbook','Метка'),
            'created_at' => Yii::t('handbook','Дата создания')
        ];
    }
}
