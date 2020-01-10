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
     * @inheritDoc/
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
                    [
                        [
                            'url',
                            'name',
                        ],
                        'safe'
                    ],
                    [
                        [
                            'url_md5_hash'
                        ],
                        'string',
                        'max' => 32
                    ]
        ];
    }

    /**
     * @inheritDoc/
     */
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
        # todd: реализовать нормальный поиск
        $query->andFilterWhere([
            'like',
            'url',
            $this->url
        ]);
        $query->andFilterWhere([
            'like',
            'name',
            $this->name
        ]);
        $query->andFilterWhere([
            'url_md5_hash' => $this->url_md5_hash
        ]);
    }
}
