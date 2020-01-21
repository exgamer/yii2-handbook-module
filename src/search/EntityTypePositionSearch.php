<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use concepture\yii2handbook\models\EntityTypePosition;

/**
 * Форма поиска по позициям сущностей в приложении
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSearch extends EntityTypePosition
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
            [
                [
                    'caption'
                ],
                'string'
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
            'like',
            'caption',
            $this->caption
        ]);
        $query->with('entityType');
    }
}
