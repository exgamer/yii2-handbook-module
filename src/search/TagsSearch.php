<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Tags;
use yii\db\ActiveQuery;

/**
 * Class TagsSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsSearch extends Tags
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'domain_id',
                    'locale',
                    'type',
                    'user_id',
                ],
                'integer'
            ],
            [['caption'], 'safe'],
        ];
    }

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
        $query->andFilterWhere([
            'type' => $this->type
        ]);
        $query->andFilterWhere([
            'user_id' => $this->user_id
        ]);
        $query->andFilterWhere([
            'like',
            'caption',
            $this->caption
        ]);
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'caption';
    }
}
