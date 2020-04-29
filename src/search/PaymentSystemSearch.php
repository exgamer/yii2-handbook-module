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
            [['id'], 'integer']
        ];
    }

    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
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
