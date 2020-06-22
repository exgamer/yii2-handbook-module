<?php

namespace concepture\yii2handbook\search;

use yii\db\ActiveQuery;
use yii\data\ActiveDataProvider;
use concepture\yii2handbook\models\Message;

/**
 * Модель поиска по переводам
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MessageSearch extends Message
{
    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
    }

    /**
     * @inheritDoc
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        parent::extendDataProvider($dataProvider);
    }
}