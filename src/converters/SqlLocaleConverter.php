<?php

namespace concepture\yii2handbook\converters;

use Yii;
use concepture\yii2logic\converters\Converter;

/**
 * Class SqlLocaleConverter
 *
 * Делает запрос на получение локалей через command, потому что через ar
 * concepture\yii2handbook\models\Locale узодит в рекурсию
 *
 * @package concepture\yii2handbook\converters
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class SqlLocaleConverter extends Converter
{
    /**
     * @param $value
     * @return mixed
     */
    public static function key($value)
    {
        $locales = Yii::$app->localeService->getCatalogBySql();
        $locales = array_flip($locales);
        if (isset($locales[$value])){
            return $locales[$value];
        }

        return $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function value($key)
    {
        $locales = Yii::$app->localeService->getCatalogBySql();
        if (isset($locales[$key])){
            return $locales[$key];
        }

        return $key;
    }
}
