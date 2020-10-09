<?php

namespace concepture\yii2handbook\components\multilanguage;

use Yii;
use yii\web\Cookie;
use yii\helpers\ArrayHelper;
use common\services\base\BaseService;
use concepture\yii2logic\db\ActiveQuery;

/**
 * Сервис мультиязычности
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
/**
 * Языковой сервис внешней части приложения
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageService extends BaseService
{
    /**
     * @var string
     */
    public $helperClass = 'concepture\yii2handbook\components\multilanguage\MultiLanguageHelper';
    /**
     * @var bool признак вывода мультиязычного конента, игнорирую доменный язык
     */
    private $use_domain_language = true;

    /**
     * @var array
     */
    private $_geoIpMap = [
        'ru' => 'ru',
        'by' => 'ru',
        'kz' => 'ru',
        'ua' => 'ru',
        'br' => 'br',
        'pt' => 'br'
    ];

    /**
     * Установка класса хэлпера
     *
     * @param string $class
     */
    public function setHelperClass(string $class)
    {
        $this->helperClass = $class;
    }

    /**
     * @param bool $value
     */
    public function setUseDomainLanguage(bool $value)
    {
        $this->use_domain_language = $value;
    }

    /**
     * Получение текущего параметра языка
     *
     * @return string
     */
    public function getCurrentLanguageParam()
    {
        return ($this->helperClass::getCurrentParam() ?? $this->helperClass::getDefaultLanguageIsoCode());
    }

    /**
     * @return bool
     */
    public function isUseDomainLanguage()
    {
        return $this->use_domain_language === true;
    }

    /**
     * Изолирует текущ язык
     *
     * @param \Closure $source
     * @return mixed
     */
    public function isolateCurrentLanguage(\Closure $source)
    {
        $this->setUseDomainLanguage(false);
        $result = call_user_func_array($source, [$this]);
        $this->setUseDomainLanguage(true);

        return $result;
    }

    /**
     * Изолирует текущий домен
     *
     * @param \Closure $source
     * @return mixed
     */
    public function isolateCurrentDomain(\Closure $source)
    {
        $this->setUseDomainLanguage(true);
        $result = call_user_func_array($source, [$this]);
        $this->setUseDomainLanguage(false);

        return $result;
    }

    /**
     * Установка куки текущего языка по iso коду страны
     *
     * @param string $iso
     *
     * @throws \common\services\LanguageServiceException
     */
    public function setCurrent(string $language_iso, $country_iso)
    {
        if(! $language_iso || ! $country_iso) {
            throw new LanguageServiceException("Language by iso: {$language_iso} is not found");
        }

        $languages = $this->getLanguages();
        $language = $languages[$language_iso] ?? null;
        if(! $language) {
            throw new LanguageServiceException("Language by iso: {$language_iso} is not found");
        }

        $countries = $this->getCountries();
        $country = $countries[$country_iso] ?? null;
        if(! $country) {
            throw new LanguageServiceException("Country by iso: {$country_iso}  is not found");
        }

        $this->setCookie($language_iso, $country_iso);
    }

    /**
     * Получение текущей локали и страны
     *
     * @return array
     */
    public function getCurrent()
    {
        $cookie = $this->getCookie();
//        if(
//            ! $cookie
//            || ! isset($cookie[$attribute]))
//        {
//            return $this->getGeoIpCountry();
//        }

        $language_iso = $cookie['language_iso'] ?? null;
        $country_iso = $cookie['country_iso'] ?? null;

        return [$language_iso , $country_iso];
    }

    /**
     * Получение полного iso кода (iso языка)-(iso страны)
     *
     * @return string
     */
    public function getCurrentFull()
    {
        list($language_iso , $country_iso) = $this->getCurrent();
        if(! $language_iso || ! $country_iso) {
            return null;
        }

        return implode('-', $this->getCurrent());
    }

    /**
     * Определение страны пользователя через модуль nginx geoip2
     *
     * @return mixed|string
     */
    public function getGeoIpCountry($default = null)
    {
        if(! isset($_SERVER['COUNTRY_CODE'])) {
            return $default ?? $this->helperClass::getDefaultLanguageIsoCode();
        }

        $iso = strtolower($_SERVER['COUNTRY_CODE']);

        return $this->_geoIpMap[$iso] ?? $default ?? $this->helperClass::getDefaultLanguageIsoCode();
    }

    /**
     * Список стран
     *
     * @return array
     */
    public function getCountries()
    {
        return $this->countryService()->getDb()->cache(function () {
            return $this->countryService()->getAllByCondition(function (ActiveQuery $query) {
                $query->indexBy('iso');
                $query->with('language');
                $query->asArray();
            });
        }, 86400);
    }

    /**
     * Список языков
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->localeService()->getDb()->cache(function () {
            return $this->localeService()->getAllByCondition(function (ActiveQuery $query) {
                $query->indexBy('locale');
                $query->asArray();
            });
        }, 86400);
    }

    /**
     * Обработка языкового параметра из адресной строки
     */
    public function languageParamProcessing()
    {
        $current_param = $this->helperClass::getCurrentParam();
        $controller = Yii::$app->controller ?? null;
        if ($controller) {
            $controller = Yii::$app->controller;
            $route = $controller->getRoute();

            # если передан язык по умолчани, делаем редирект на урл без языка
            if(null !== $current_param && $current_param == $this->helperClass::getDefaultLanguageIsoCode()) {
                $params = Yii::$app->getRequest()->getQueryParams();
                unset($params['language']);

                return $controller->redirect(ArrayHelper::merge(["/{$route}"], $params), 302);
            }
            # если главная страница футзала без параметра и есть кука, редирект на язык из куки
            if(null == $current_param && $route === 'futsal/site/index') {
                $iso = $this->getCurrentFull();
                if($iso) {
                    $language_param = $this->helperClass::getParamByIsoCode($iso);
                    if($language_param && $language_param !== $this->helperClass::getDefaultLanguageIsoCode()) {

                        return $controller->redirect(["/{$route}", 'language' => $language_param], 302);
                    }
                }
            }
        }

        $current_iso = $this->helperClass::getCurrentIsoCode();
        list($current_language_iso, $current_country_iso) = $this->helperClass::parseIsoCode($current_iso, $current_param);
        list($storage_language_iso, $storage_country_iso) = $this->getCurrent();
        if ($storage_language_iso !== $current_language_iso || $storage_country_iso !== $current_country_iso) {
            $this->setCurrent($current_language_iso, $current_country_iso);
        }

        Yii::$app->language = $current_language_iso;

        return true;
    }

    /**
     * @return mixed
     */
    private function getCookie()
    {
        $cookies = Yii::$app->getRequest()->cookies;

        return $cookies->getValue($this->helperClass::CURRENT_LANGUAGE_COOKIE_NAME, null);
    }

    /**
     * Установка куки языка
     *
     * @param string $language_iso
     * @param string $country_iso
     */
    private function setCookie($language_iso, $country_iso)
    {
        $cookies = Yii::$app->getResponse()->cookies;
        $cookies->add(new Cookie([
            'name' => $this->helperClass::CURRENT_LANGUAGE_COOKIE_NAME,
            'value' => [
                'language_iso' => $language_iso,
                'country_iso' => $country_iso,
            ]
        ]));
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class LanguageServiceException extends \Exception {}