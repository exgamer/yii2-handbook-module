<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Переводы по локалям
 *
 * @property int $id
 * @property string $language
 * @property string $translation
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Message extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{message}}';
    }

    /**
     * @inheritDoc
     */
    public static function label()
    {
        return Yii::t('common', 'Переводы');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceMessage()
    {
        return $this->hasOne(SourceMessage::class, ['id' => 'id']);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('common', 'Язык'),
            'category' => Yii::t('common', 'Перевод'),
        ];
    }
}