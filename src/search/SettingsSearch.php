<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Settings;

/**
 * Class SettingsSearch
 * @package concepture\yii2handbook\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsSearch extends Settings
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'domain_id',
                    'locale',
                ],
                'integer'
            ],
            [['name'], 'safe'],
        ];
    }
}
