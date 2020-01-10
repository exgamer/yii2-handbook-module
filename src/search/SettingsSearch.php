<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Settings;
use yii\db\ActiveQuery;

/**
 * Class SettingsSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsSearch extends Settings
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
                ],
                'integer'
            ],
            [
                [
                    'name'
                ],
                'safe'
            ],
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
            'like',
            'name',
            $this->name
        ]);
    }

    public static function getListSearchKeyAttribute()
    {
        return 'name';
    }

    public static function getListSearchAttribute()
    {
        return 'value';
    }
}
