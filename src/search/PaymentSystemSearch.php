<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\PaymentSystem;
use yii\db\ActiveQuery;

/**
 * Class PaymentSystemSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PaymentSystemSearch extends PaymentSystem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [
                [
                    'name'
                ],
                'string'
            ],
        ];
    }

    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere([
            'like',
            'name',
            $this->name
        ]);
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'name';
    }
}
