<?php

namespace concepture\yii2handbook\v2\models;

use concepture\yii2logic\models\ActiveRecord;

/**
 *
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsProperty extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dynamic_elements_property';
    }
}
