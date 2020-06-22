<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Country;
use concepture\yii2logic\db\ActiveQuery;
use concepture\yii2logic\helpers\UrlHelper;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use concepture\yii2handbook\models\Domain;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\traits\ConfigAwareTrait;

/**
 * @todo перевести функции работающие с domainMap в статику
 * @todo избавиться от AppHelper в common, логика должна быть в этом классе, статические методы
 * @todo возможность указать хэлпер из конфига, единый интерфейс для конфига
 * @todo чтобы не распылять по двум классам
 *
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainService extends Service
{
    use ConfigAwareTrait;

    /**
     * @var string
     */
    public $cookieName = 'current_domain';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->setDomainMap();
    }

    /**
     * Возвращает карту доменов из параметров
     *
     * @return mixed|null
     */
    public function setDomainMap()
    {
        if (! isset(Yii::$app->params['yii2handbook'])){

            return null;
        }

        if (! isset(Yii::$app->params['yii2handbook']['domainMap'])){

            return null;
        }

        $this->setConfig(['domainMap' => Yii::$app->params['yii2handbook']['domainMap']]);
    }

    /**
     * @return array
     */
    public function getDomainMap()
    {
        return $this->getConfigItem('domainMap');
    }

    /**
     * Возвращает все данные по domainMap
     *
     * @return array
     */
    public function getDomainsData()
    {
        static $_items = null;
        if ($_items){
            return $_items;
        }

        $items = $this->modelsCatalog();
        $map = $this->getDomainMap();
        foreach ($map as $url => $data){
            $data['host'] = $url;
            $data['host_with_scheme'] = UrlHelper::getCurrentSchema() . "://" . $url;
            $data['language_id'] = $languageId = Yii::$app->localeService->catalogValue($data['language'], 'locale', 'id');
            $data['locale_caption'] = Yii::$app->localeService->catalogValue($data['locale'], 'locale', 'caption');
            $data['country_id'] = $countryId = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'id');
            $data['country_caption'] = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'caption');

            // TODO нужно как то лакончиней сделать
            $country_caption_by_language = Yii::$app->countryService->getOneByCondition(function (ActiveQuery $query) use ($languageId, $countryId) {
                $query->resetCondition();
                $query->select(Country::localizationAlias().'.caption');
                $query->andWhere([
                    'id' => $countryId,
                    Country::localizationAlias().'.entity_id' => $countryId,
                    Country::localizationAlias().'.locale' => $languageId,
                ]);
            });
            if ($country_caption_by_language) {
                $data['country_caption_by_language'] = $country_caption_by_language->caption;
            }

            $data['country_image'] = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'image');
            $urlArray = explode('.', $url);
            array_shift($urlArray);
            $url = implode('.', $urlArray);
            $data['domain'] = ".".$url;

            $domainData[$data['alias']] = $data;
        }

        $result = [];
        foreach ($items as $id => $data){
            if ($domainData[$data->alias]){
                $domainData[$data->alias]['domain_id'] = $id;
                $result[$id] = $domainData[$data->alias];
            }
        }

        $_items = $result;

        return $result;
    }


    /**
     * Возвращает домены для которых есть свойства у модели
     *
     * @param $model
     * @param string $propsRelationName
     * @return array
     */
    public function getModelDomains($model = null, $propsRelationName = 'properties')
    {

        static $items = null;

        $result = $this->getDomainsData();
        if (! $items){
            $items = $this->modelsCatalog();
        }

        if (! $model){
            return $result;
        }

        $res = [];
        $method = "get" . ucfirst($propsRelationName);
        $query = $model->{$method}();
        unset($query->where['domain_id']);
        $models = $query->all();
        if ($models) {
            foreach ($models as $property) {
                $domain = $items[$property->domain_id] ?? null;
                if (! $domain){
                    continue;
                }

                $res[$property->domain_id] = $result[$property->domain_id];
            }
        }

        return $res;
    }

    /**
     * Возвращает данные по текущему домену
     *
     * @return array
     */
    public function getCurrentDomainData()
    {
        $items = $this->getDomainsData();

        return $items[$this->getCurrentDomainId()] ?? null;
    }

    /**
     * Возвращает данные по текущему домену по хосту
     *
     * @param $host
     * @return array
     */
    public function getDomainDataByHost($host = null)
    {
        if (! $host){
            $host = $this->getCurrentHost();
        }

        $items = $this->getDomainsData();

        foreach ($items as $item){
            if ($item['host'] == $host){
                return $item;
            }
        }

        return  null;
    }

    /**
     * Возвращает локали из списка доменов
     *
     * @return array
     */
    public function getDomainMapLocales()
    {
        return $this->getDomainMapAttributes('locale');
    }

    /**
     * Получение всех атрибутов из домен мапы
     *
     * @param string $attribute
     *
     * @return array
     */
    public function getDomainMapAttributes($attribute)
    {
        $result = [];
        $map =  $this->getDomainMap();
        if (! $map) {
            return $result;
        }

        foreach ($map as $host => $data) {
            if (
                ! is_array($data)
                || ! isset($data[$attribute])
            ) {
                continue;
            }

            if(! is_array($data[$attribute])) {
                $result[$data[$attribute]] = $data[$attribute];
            } else {
                foreach ($data[$attribute] as $value) {
                    $result[$value] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Установка куки текущего альяса домена
     *
     * @param string $alias
     */
    public function setCookie(string $alias)
    {
        $cookies = Yii::$app->getResponse()->cookies;
        $cookies->add(new Cookie([
            'name' => $this->cookieName,
            'value' => $alias,
        ]));
    }

    /**
     * Получение куки текущего домена
     *
     * @return mixed
     */
    public function getCookie()
    {
        $cookies = Yii::$app->getRequest()->cookies;

        return $cookies->getValue($this->cookieName);
    }

    /**
     * Возвращает локаль по domain map
     *
     * @return integer
     */
    public function getLocaleByDomainMap()
    {
        $domainMap = $this->getDomainMap();
        if (empty($domainMap)){
            return null;
        }

        $domainMap = ArrayHelper::index($domainMap, 'alias');
        $currentDomain = $this->getCurrentDomain();
        if (empty($currentDomain)){
            throw new \Exception("curernt domain not found");
        }

        $locale = $domainMap[$currentDomain->alias]['locale'] ?? null;
        if (! $locale){
            throw new \Exception("curerent domain locale unknown");
        }

        return $locale;
    }

    /**
     * Возвращает атрибуты из карты доменов по альясу
     *
     * @param string $alias
     * @return array
     * @throws DomainServiceException
     */
    public function getAttributesByAlias($alias)
    {
        $domainMap = $this->getDomainMap();
        if (empty($domainMap)){
            throw new DomainServiceException('Params yii2handbook domainMap must be set');
        }

        $items = [];
        foreach ($domainMap as $host => $settings) {
            $items[$settings['alias']] = ArrayHelper::merge(['host' => $host], $settings);
        }

        if(! isset($items[$alias]) ) {
            throw new DomainServiceException('yii2handbook DomainMap alias is not found.');
        }

        return $items[$alias];
    }

    /**
     * Возвращает атрибут из карты доменов по альясу
     *
     * @param string $alias
     * @param string $attribute
     * @throws DomainServiceException
     */
    public function getAttributeByAlias($alias, $attribute)
    {
        $items = $this->getAttributesByAlias($alias);

        if(! isset($items[$attribute])) {
            throw new DomainServiceException('Attribute is not found.');
        }

        return $items[$attribute];
    }

    /**
     * Установка виртуального текущего ид домена
     *
     * @param $domain_id
     */
    public function setVirtualDomainId($domain_id)
    {
        $GLOBALS['VIRTUAL_DOMAIN_ID'] = $domain_id;
    }

    /**
     * Получение виртуального текущего ид домена
     *
     */
    public function getVirtualDomainId()
    {
        if (isset($GLOBALS['VIRTUAL_DOMAIN_ID'])){
            return $GLOBALS['VIRTUAL_DOMAIN_ID'];
        }

        return null;
    }

    /**
     * Удаление виртуального текущего ид домена
     *
     */
    public function clearVirtualDomainId()
    {
        unset($GLOBALS['VIRTUAL_DOMAIN_ID']);
    }

    /**
     * Возвращает id текущего домена
     *
     * @param bool $reset
     *
     * @return int|null
     */
    public function getCurrentDomainId($reset = false)
    {
        if ($this->getVirtualDomainId()){
            return $this->getVirtualDomainId();
        }

        static $result;

        if($result && ! $reset) {

            return $result;
        }

        $domains = $this->catalog();
        $domains = array_flip($domains);
        if (! Yii::$app instanceof \yii\web\Application) {
            /**
             * Для проектов где используется --alias при вызове консольных команд
             * должно быть установлена переменная APP_ALIAS для получения domain_id
             */
            if (defined('APP_ALIAS')) {
                return $domains[APP_ALIAS] ?? null;
            }
        }

        $domainMap = $this->getDomainMap();
        if ($domainMap == null){
            return null;
        }

        $host = $this->getCurrentHost();
        if(! $host) {
            return null;
        }

        $domainInfo = [];
        foreach ($domainMap as $domain => $info){
            if (is_array($info)){
                $alias = $info['alias'] ?? null;
            }else{
                $alias = $info;
            }

            $domainInfo[$domain] = $alias;
        }

        if (! isset($domainInfo[$host])){
            $domainInfo = array_flip($domainInfo);
            if (! isset($domainInfo[$host])){
                return null;
            }

            $host = $domainInfo[$host];
            $domainInfo = array_flip($domainInfo);
        }

        $domainAlias = $domainInfo[$host];


        if (! isset($domains[$domainAlias])){

            return null;
        }

        return $domains[$domainAlias];
    }

    /**
     * Возвращает текущий хост
     *
     * @return string
     */
    public function getCurrentHost()
    {
        static $result;

        if($result) {
            return $result;
        }

        $currentDomain = null;
        if (! Yii::$app instanceof \yii\web\Application) {
            if (isset($GLOBALS['VIRTUAL_HOST'])){
                return $GLOBALS['VIRTUAL_HOST'];
            }

            /**
             * Для проектов где используется --alias при вызове консольных команд
             * должно быть установлена переменная APP_HOST для получения хоста из консольки
             */
            if (defined('APP_HOST')){
                return APP_HOST;
            }

            return null;
        }

        return $this->getRealCurrentHost();
    }

    /**
     * Возвращает текущий хост
     *
     * @return string
     */
    public function getRealCurrentHost()
    {
        if (! Yii::$app instanceof \yii\web\Application) {
            if (isset($GLOBALS['VIRTUAL_HOST'])){
                return $GLOBALS['VIRTUAL_HOST'];
            }

            /**
             * Для проектов где используется --alias при вызове консольных команд
             * должно быть установлена переменная APP_HOST для получения хоста из консольки
             */
            if (defined('APP_HOST')){
                return APP_HOST;
            }

            return null;
        }

        $currentDomain = Url::base(true);
        $parsed = parse_url($currentDomain);

        return $parsed['host'];
    }

    /**
     * Возвращает запись текущего домена
     *
     * @param bool $reset
     *
     * @return Domain
     */
    public function getCurrentDomain($reset = false)
    {
        static $result;

        if($result && ! $reset) {
            return $result;
        }

        $currentDomainId = $this->getCurrentDomainId();
        $result = $this->getCatalogModel($currentDomainId);

        return $result;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DomainServiceException extends \Exception
{

}