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
    * {@inheritdoc}
    */
    public function rules()
    {
        return [


        ];
    }

    /**
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query)
    {

    }
}
