<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Sitemap;
use yii\db\ActiveQuery;

/**
 * Class SitemapSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapSearch extends Sitemap
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
                    'type',
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
            static::tableName().'.type' => $this->type
        ]);
        $query->andFilterWhere([
            static::tableName().'.domain_id' => $this->domain_id
        ]);
        $query->andFilterWhere([
            static::tableName().'.is_deleted' => $this->is_deleted
        ]);
    }
}
