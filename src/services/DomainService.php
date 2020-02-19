<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Cookie;
use concepture\yii2handbook\models\Domain;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\traits\ConfigAwareTrait;

/**
 * Class DomainService
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