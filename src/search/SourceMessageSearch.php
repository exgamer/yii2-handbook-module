<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\enum\MessageCategoryEnum;
use concepture\yii2handbook\models\Message;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use concepture\yii2logic\enum\AccessEnum;
use concepture\yii2handbook\models\SourceMessage;

/**
 * Модель поиска по оригиналам переводов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SourceMessageSearch extends SourceMessage
{
    /**
     * @var array
     */
    public $ids = [];

    /**
     * @var string
     */
    public $translation;

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
     * @var string
     */
    public $messageLanguage;

    /**
     * @var
     */
    public $is_empty;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'message',
                    'translation',
                    'category',
                    'messageLanguage'
                ],
                'string'
            ],
            [
                [
                    'ids',
                ],
                'each',
                'rule' => ['integer']
            ],
            [
                [
                    'is_empty'
                ],
                'boolean'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterFind()
    {
        $this->messageCount = $this->messages;
        $count = count($this->messageCount);
        if($this->messageLanguage) {
            $count = 1;
        }

        $fillCount = 0;
        foreach ($this->messageCount as $message) {
            if($message->language == 'ru-ru') {
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

    /**
     * @return string
     */
    protected function getMessageTableName()
    {
        return Message::tableName();
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $sourceMessageTableName = static::tableName();
        $messageTableName = $this->getMessageTableName();
        $query->innerJoinWith(['messages']);
        $query->andFilterWhere(['like', "{$sourceMessageTableName}.message", $this->message]);
        $query->andFilterWhere(['like',  "{$messageTableName}.translation", $this->translation]);
        $query->andFilterWhere([
            "{$sourceMessageTableName}.id" => $this->ids,
            "{$sourceMessageTableName}.category" =>$this->category,
        ]);
        if (!\Yii::$app->user->can(AccessEnum::SUPERADMIN)) {
            $query->andFilterWhere(['category' => static::visibleCategories()]);
            $query->andFilterWhere(['not like',  "{$sourceMessageTableName}.message", '@@']);
        }

        $query->groupBy(['message', 'category', 'id']);
        if($this->is_empty == 1) {
            $query->andWhere("{$messageTableName}.translation = '' OR {$messageTableName}.translation is null");
        }

        if($this->messageLanguage) {
            $query->andWhere(["{$messageTableName}.language" => [$this->messageLanguage]]);
        }
    }

    /**
     * Видимые категории переводов для всех
     *
     * @return array
     */
    public static function visibleCategories()
    {
        return [
            MessageCategoryEnum::FRONTEND,
            MessageCategoryEnum::GENERAL
        ];
    }

    /**
     * @inheritDoc
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        parent::extendDataProvider($dataProvider);
        if($dataProvider->pagination) {
            $dataProvider->pagination->pageSize = 50;
        }
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'translation' => Yii::t('common', 'Перевод'),
                'is_empty' => Yii::t('common', 'Перевод не заполнен'),
                'messageLanguage' => Yii::t('common', 'Версия'),
            ]
        );
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
}