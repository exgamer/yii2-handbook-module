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
     * @var array
     */
    public $ids;

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
                        'string'
                    ],
                    [
                        [
                            'route_hash'
                        ],
                        'string',
                        'max' => 32
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
     * @inheritDoc/
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere([
            'like',
            'lower(route)',
            $this->route
        ]);
        $query->andFilterWhere([
            'like',
            'lower(name)',
            $this->name
        ]);
        $query->andFilterWhere([
            'route_hash' => $this->route_hash
        ]);
        $query->andFilterWhere([
            'id' => $this->ids
        ]);
    }
}
