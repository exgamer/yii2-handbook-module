<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\Robots;

/**
 * Поиск по индексным файлам - robots.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RobotsSearch extends Robots
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'status',
                    'domain_id',
                    'is_deleted',
                ],
                'integer'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            static::tableName().'.id' => $this->id
        ]);
        $query->andFilterWhere([
            'status' => $this->status
        ]);
        $query->andFilterWhere([
            'domain_id' => $this->domain_id
        ]);
        $query->andFilterWhere([
            'is_deleted' => $this->is_deleted
        ]);
    }
}
