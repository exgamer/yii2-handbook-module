<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\DynamicElements;

/**
 * Поиск по динамическим элементам
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
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
                            'domain_id',
                            'locale',
                        ],
                        'integer'
                    ],
                    [
                        [
                            'url',
                            'name',
                        ],
                        'string'
                    ],
                    [
                        [
                            'url_md5_hash'
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
            'domain_id' => $this->domain_id
        ]);
        $query->andFilterWhere([
            'locale' => $this->locale
        ]);
        # todd: реализовать нормальный поиск
        $query->andFilterWhere([
            'like',
            'url',
            $this->url
        ]);
        $query->andFilterWhere([
            'like',
            'name',
            $this->name
        ]);
        $query->andFilterWhere([
            'url_md5_hash' => $this->url_md5_hash
        ]);
    }
}
