<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\SeoBlock;

/**
 * Поиск по сео блокам
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockSearch extends SeoBlock
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id'
                ],
                'integer'
            ],
            [
                [
                    'caption'
                ],
                'string'
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere([
            'like',
            'caption',
            $this->caption
        ]);
    }
}
