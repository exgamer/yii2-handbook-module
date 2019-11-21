<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\SeoSettings;
use yii\db\ActiveQuery;

/**
 * Class SeoSettingsSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsSearch extends SeoSettings
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
                ],
                'integer'
            ],
            [['url'], 'safe'],
        ];
    }

    protected function extendQuery(ActiveQuery $query)
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
            'url',
            $this->url
        ]);
    }
}
