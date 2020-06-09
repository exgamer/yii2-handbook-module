<?php

namespace concepture\yii2handbook\v2\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\v2\models\DynamicElements;

/**
 * Поиск по динамическим элементам версия 2
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsSearch extends DynamicElements
{
    /**
     * @inheritDoc/
     */
    public function rules()
    {
        return [
                    [
                        [
                            'id',
                        ],
                        'integer'
                    ],
                    [
                        [
                            'route',
                            'name',
                        ],
                        'safe'
                    ],
                    [
                        [
                            'route_hash'
                        ],
                        'string',
                        'max' => 32
                    ]
        ];
    }

    /**
     * @inheritDoc/
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere([
            'like',
            'route',
            $this->route
        ]);
        $query->andFilterWhere([
            'like',
            'name',
            $this->name
        ]);
        $query->andFilterWhere([
            'route_hash' => $this->route_hash
        ]);
    }
}
