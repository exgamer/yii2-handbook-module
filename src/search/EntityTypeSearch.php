<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\EntityType;

/**
 * Class EntityTypeSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityTypeSearch extends EntityType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['table_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public static function getListSearchAttribute()
    {
        return 'caption';
    }
}
