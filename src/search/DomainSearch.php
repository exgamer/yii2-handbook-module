<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Domain;

/**
 * Class DomainSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainSearch extends Domain
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['domain'], 'safe'],
        ];
    }
}
