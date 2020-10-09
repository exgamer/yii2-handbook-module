<?php

namespace concepture\yii2handbook\components\multilanguage;

use Yii;

/**
 * Хэлпер мультиязычности
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageHelper
{
    const CURRENT_LANGUAGE_COOKIE_NAME = 'current_language';

    /**
     * Получение параметра текущего языка
     *
     * @return array|mixed
     */
    public static function getCurrentParam()
    {
        return Yii::$app->getRequest()->get('language');
    }

    /**
     * Получение iso кода текущего языка (язык-страна)
     *
     * @return string
     */
    public static function getCurrentIsoCode()
    {
        $param = static::getCurrentParam() ?? (static::getDefaultLanguageIsoCode());

        return static::getIsoCodeByParam($param);
    }

    /**
     * @return string
     */
    public static function getDefaultLanguageIsoCode()
    {
        return 'ru';
    }

    /**
     * @return string
     */
    public static function getDefaultCountryIsoCode()
    {
        return 'ru';
    }

    /**
     * Карта языков, ключ параметр - значение полный iso (язык-страна)
     *
     * @return array
     */
    public static function getLanguageMap()
    {
        return [];
    }

    /**
     * Возвращает допустимые параметры языков
     *
     * @return array
     */
    public static function getAllowedParams()
    {
        return array_keys(static::getLanguageMap());
    }

    /**
     * Возвращает допустимые значения полных iso (язык-страна)
     *
     * @return array
     */
    public static function getAllowedIsoCodes()
    {
        return array_values(static::getLanguageMap());
    }

    /**
     * Возаращает полный iso по параметру
     *
     * @param string $param
     *
     * @return string
     */
    public static function getIsoCodeByParam(string $param)
    {
        $map = static::getLanguageMap();

        return $map[$param] ?? (static::getDefaultLanguageIsoCode() . "-" . static::getDefaultCountryIsoCode());
    }

    /**
     * Возаращает праметр по полному iso
     *
     * @param string $iso_code
     *
     * @return string
     */
    public static function getParamByIsoCode(string $iso_code)
    {
        $map = array_flip(static::getLanguageMap());

        return $map[$iso_code] ?? static::getDefaultLanguageIsoCode();
    }

    /**
     * Разбор полного iso кода на две части (язык-страна)
     *
     * @param string $iso_code
     *
     * @return array
     */
    public static function parseIsoCode(string $iso_code)
    {
        return explode('-', $iso_code);
    }
}