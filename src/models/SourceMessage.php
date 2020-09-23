<?php

namespace concepture\yii2handbook\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Оригиналы переводов
 *
 * @property int $id
 * @property string $category
 * @property string $message
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SourceMessage extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{source_message}}';
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
    public function getMessages()
    {
//        $countries = \Yii::$app->domainService->getDomainMapAttributes('country');

//        return $this->hasMany(Message::class, ['id' => 'id'])->andWhere(['in', 'language', $countries]);

        return $this->hasMany(Message::class, ['id' => 'id']);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('common', 'Оригинал'),
            'category' => Yii::t('common', 'Словарь'),
            'messageState' => Yii::t('common', 'Переводы'),
            'defaultTranslation' => Yii::t('common', 'Русский')
        ];
    }
}