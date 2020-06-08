<?php

namespace concepture\yii2handbook\search;

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
                ],
                'string'
            ]
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
            $query->andFilterWhere(['not like', static::tableName() . ".message", '@@']);
        }

        $query->groupBy(['message', 'category', 'id']);
    }

    /**
     * @inheritDoc
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        $dataProvider->pagination->pageSize = 150;
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