<?php

namespace concepture\yii2locale\search;

use concepture\yii2locale\models\Locale;

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
}
