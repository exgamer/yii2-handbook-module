<?php

namespace concepture\yii2handbook\search;
use yii\db\ActiveQuery;
use concepture\yii2handbook\models\StaticFile;

/**
 * Class StaticFileSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileSearch extends StaticFile
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
                        'status',
                        'domain_id',
                        'is_deleted',
                    ],
                    'integer'
                ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            static::tableName().'.id' => $this->id
        ]);
        $query->andFilterWhere([
            static::tableName().'.status' => $this->status
        ]);
        $query->andFilterWhere([
            static::tableName().'.domain_id' => $this->domain_id
        ]);
        $query->andFilterWhere([
            static::tableName().'.is_deleted' => $this->is_deleted
        ]);
        $query->andWhere([
            static::tableName().'.is_hidden' => 0
        ]);
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
        return 'filename';
    }
}
