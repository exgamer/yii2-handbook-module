<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
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
                    'id',
                    'entity_type_position_id',
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
        $query->andFilterWhere([
            static::tableName().'.entity_type_position_id' => $this->entity_type_position_id
        ]);
    }

    /**
     * @inheritDoc
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        parent::extendDataProvider($dataProvider);
        $dataProvider->pagination = false;
        $dataProvider->getSort()->defaultOrder = ['sort' => SORT_ASC];
    }
}
