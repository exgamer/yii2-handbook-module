<?php

namespace concepture\yii2handbook\search;

use concepture\yii2handbook\models\Locale;

class LocaleSearch extends Locale
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['locale'], 'safe'],
        ];
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'locale';
    }
}
