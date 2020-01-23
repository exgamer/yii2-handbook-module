<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\EntityTypePositionSort;

/**
 * Форма поиска сортировки сущностей по позиции в приложении
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortSearch extends EntityTypePositionSort
{
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [
                [
                    'id'
                ],
                'integer'
            ],
        ];
    }

    /**
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            static::tableName().'.id' => $this->id
        ]);
    }
}
