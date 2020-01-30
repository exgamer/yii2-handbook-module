<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\Robots;

/**
 * Class SitemapSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapSearch extends Robots
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
            static::tableName().'.status' => $this->status
        ]);
        $query->andFilterWhere([
            static::tableName().'.domain_id' => $this->domain_id
        ]);
        $query->andFilterWhere([
            static::tableName().'.is_deleted' => $this->is_deleted
        ]);
    }
}
