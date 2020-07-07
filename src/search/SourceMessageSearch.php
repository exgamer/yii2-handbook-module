<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\enum\MessageCategoryEnum;
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->innerJoinWith(['messages']);
        $query->andFilterWhere(['like', static::tableName() . ".message", $this->message]);
        $query->andFilterWhere(['like',  "message.translation", $this->translation]);
        if (!\Yii::$app->user->can(AccessEnum::SUPERADMIN)) {
            $query->andFilterWhere(['category' => [MessageCategoryEnum::FRONTEND, MessageCategoryEnum::GENERAL]]);
            $query->andFilterWhere(['not like', static::tableName() . ".message", '@@']);
        } else {
            $query->andFilterWhere(['like', static::tableName() . ".category", $this->category]);
        }

        $query->groupBy(['message', 'category', 'id']);
        $query->andFilterWhere([
            static::tableName() . '.id' => $this->ids
        ]);
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
                'translation' => Yii::t('common', 'Перевод')
            ]
        );
    }
}