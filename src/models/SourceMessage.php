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
     * @var string
     */
    public $defaultTranslation;

    /**
     * @var int
     */
    public $messageCount = [];

    /**
     * @var int
     */
    public $allCount;

    /**
     * @var int
     */
    public $fillCount;

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
        $countries = \Yii::$app->domainService->getDomainMapAttributes('country');
        return $this->hasMany(Message::class, ['id' => 'id'])->andWhere(['in', 'language', $countries]);
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

    /**
     * Состояние сообщений
     *
     * @return string
     */
    public function getMessageState()
    {
        return "{$this->fillCount}/{$this->allCount}";
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->messageCount = $this->messages;
        $count = count($this->messageCount);
        $fillCount = 0;
        foreach ($this->messageCount as $message) {
            if($message->language == 'ru') {
                $this->defaultTranslation = $message->translation;
            }

            if(! empty($message->translation)) {
                $fillCount ++;
            }
        }

        $this->allCount = $count;
        $this->fillCount = $fillCount;

        parent::afterFind();
    }
}