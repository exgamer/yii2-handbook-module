<?php

namespace concepture\yii2handbook\search;


use concepture\yii2handbook\models\UrlHistory;
use yii\db\ActiveQuery;

/**
 * Class UrlHistorySearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UrlHistorySearch extends UrlHistory
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
                    'domain_id',
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
            static::tableName().'.domain_id' => $this->domain_id
        ]);
    }
}
