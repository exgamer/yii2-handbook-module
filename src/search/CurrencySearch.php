<?php
namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\Currency;

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
            [['name'], 'string'],
            [['code'], 'string'],
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
        $query->andFilterWhere([
            'like',
            'lower('.static::localizationAlias() . '.name)',
            $this->name
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
