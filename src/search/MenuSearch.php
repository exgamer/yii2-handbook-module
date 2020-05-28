<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\Menu;

/**
 * Class MenuSearch
 *
 * @package concepture\yii2handbook\search
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuSearch extends Menu
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
                    'type',
                    'domain_id',
                    'status',
                ],
                'integer',
            ],
            [
                [
                    'caption',
                ],
                'string',
            ],
        ];
    }

    /**
     * @inheritDoc/
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'domain_id' => $this->domain_id,
            'status' => $this->status,
        ]);
        $query->andFilterWhere([
            'like',
            'caption',
            $this->caption
        ]);
    }
}
