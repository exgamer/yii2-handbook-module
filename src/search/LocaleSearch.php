<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Locale;

class LocaleSearch extends Locale
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['locale'], 'safe'],
        ];
    }

    protected function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere([
            'like',
            'locale',
            $this->locale
        ]);
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'locale';
    }
}
