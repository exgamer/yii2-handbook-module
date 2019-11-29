<?php
namespace concepture\yii2handbook\converters;

use concepture\yii2logic\converters\Converter;
use Yii;

/**
 * Класс для конвертации локали из таблицы
 *
 * Class LocaleConverter
 * @package concepture\yii2handbook\converters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleConverter extends Converter
{
    public static function key($value)
    {
        $locales = Yii::$app->localeService->catalog('id', 'locale');
        $locales = array_flip($locales);
        if (isset($locales[$value])){
            return $locales[$value];
        }

        return $value;
    }

    public static function value($key)
    {
        $locales = Yii::$app->localeService->catalog();
        if (isset($locales[$key])){
            return $locales[$key];
        }

        return $key;
    }
}
