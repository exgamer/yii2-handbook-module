<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Currency;
use yii\db\ActiveQuery;

/**
 * Class LocaleSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CurrencySearch extends Currency
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['code'], 'safe'],
        ];
    }

    /**
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'code' => $this->code,
        ]);
    }

    /**
     * @return string
     */
    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public static function getListSearchAttribute()
    {
        return 'code';
    }
}
