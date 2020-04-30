<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Country;
use yii\db\ActiveQuery;

/**
 * Class CountrySearch
 * @package concepture\yii2handbook\search
 */
class CountrySearch extends Country
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'locale', 'domain_id'], 'integer'],
            [['caption', 'iso'], 'safe'],
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
            'like',
            'caption',
            $this->caption
        ]);
        $query->andFilterWhere([
            'like',
            'iso',
            $this->iso
        ]);
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'iso';
    }
}
