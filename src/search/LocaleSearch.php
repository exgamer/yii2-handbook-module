<?php
namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\Locale;

/**
 * Class LocaleSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleSearch extends Locale
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['caption'], 'string'],
            [['locale'], 'safe'],
        ];
    }

    /**
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere([
            'like',
            'lower('.static::localizationAlias() . '.locale)',
            $this->locale
        ]);
        $query->andFilterWhere([
            'like',
            'lower('.static::localizationAlias() . '.caption)',
            $this->caption
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
        return 'locale';
    }
}
