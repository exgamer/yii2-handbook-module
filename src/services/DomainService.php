<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Country;
use concepture\yii2logic\db\ActiveQuery;
use concepture\yii2logic\helpers\UrlHelper;
use Exception;
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
     * Используется для сущностей справочников где основным критерием является locale_id
     * в этом случае если domain_id не соответствует указанному locale_id то будет подставлен domain_id согласно locale_id
     *
     * Возвращает массив с Id домена и локали
     * Используется в моделях для метода  uniqueFieldValue() для получения корректных данных по domain_id и locale_id с учетом languages в domain map
     */
    public function getResolvedCurrentDomainAndLocale()
    {
        $domain_id = $this->getCurrentDomainId();
        $locale_id = $this->getCurrentDomainLocaleId();
        // если явно передан гет параметр подставляем его
        if (
            Yii::$app->has('request')
            && Yii::$app->getRequest() instanceof \yii\web\Request
            && Yii::$app->getRequest()->getQueryParam('locale_id')
        ) {
            $locale_id = Yii::$app->getRequest()->getQueryParam('locale_id');
        }

        $domainsData = $this->getDomainsData();
        $domainsDataByAlias = \yii\helpers\ArrayHelper::index($domainsData, 'alias');
        $editedDomainData = $domainsData[$domain_id];
        if (isset($editedDomainData['languages']) && ! empty($editedDomainData['languages'])) {
            foreach ($editedDomainData['languages'] as $domain => $language) {
                $data = $domainsDataByAlias[$domain];
                $lang_domain_id = $data['domain_id'];
                $lang_locale_id = Yii::$app->localeService->catalogKey($language, 'id', 'locale');
                if ($lang_locale_id == $locale_id) {
                    $domain_id = $lang_domain_id;
                    break;
                }
            }
        }

        return [
            "domain_id" => $domain_id,
            "locale_id" => $locale_id,
        ];
    }

    /**
     * Установит язык в зависимости от домена
     *
     * Если $domainByLocale = true domain_id будет установлен в зависимости от ключа из domain-map['languages']
     *
     * @param $domain_id
     * @param $locale_id
     * @param bool $domainByLocale
     */
    public function resolveLocaleId(&$domain_id, &$locale_id, $domainByLocale = false)
    {
        if ($domain_id && $locale_id) {

            return;
        }

        if (! $domainByLocale) {
            $locale_id = $this->getDomainLocaleId($domain_id);

            return;
        }

        //Для случая создания сущности, когда у домена указаны используемые языки версий, чтобы подставить верную связку домена и языка
        if ($domainByLocale) {
            $domainsData = $this->getDomainsData();
            $domainsDataByAlias = \yii\helpers\ArrayHelper::index($domainsData, 'alias');
            $editedDomainData = $domainsData[$domain_id];
            if (isset($editedDomainData['languages']) && ! empty($editedDomainData['languages'])) {
                foreach ($editedDomainData['languages'] as $domain => $language) {
                    $data = $domainsDataByAlias[$domain];
                    $domain_id = $data['domain_id'];
                    $locale_id = Yii::$app->localeService->catalogKey($language, 'id', 'locale');
                    break;
                }
            }
        }
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
     * @param \Closure $extendData function($data) { ... $data['a'] = 'b' return $data;}
     *
     * @return array
     */
    public function getDomainsData(\Closure $extendData = null)
    {
        static $_items = null;
        if ($_items){
            return $_items;
        }

        $items = $this->modelsCatalog();
        $map = $this->getDomainMap();
        foreach ($map as $url => $data) {
            $data['host'] = $url;
            if (Yii::$app instanceof \yii\web\Application) {
                $data['host_with_scheme'] = UrlHelper::getCurrentSchema() . "://" . $url;
            }

            $data['language_id'] = $languageId = Yii::$app->localeService->catalogValue($data['language'], 'locale', 'id');
            $data['locale_caption'] = Yii::$app->localeService->catalogValue($data['locale'], 'locale', 'caption');
            $data['country_id'] = $countryId = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'id');
            $data['country_caption'] = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'caption');
            $data['country_currency_id'] = Yii::$app->countryService->catalogValue($data['country'], 'iso', 'currency_id');
            if($extendData) {
                $data = call_user_func($extendData, $data);
            }

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
            if (isset($domainData[$data->alias])){
                $domainData[$data->alias]['domain_id'] = $id;
                $result[$id] = $domainData[$data->alias];
            }
        }

        $_items = $result;

        return $result;
    }

    /**
     * Получение данных по включенным доменам
     *
     * @return array
     */
    public function getEnabledDomainData()
    {
        static $result;

        if(! $result) {
            $data = $this->getDomainsData();
            $result =  array_filter($data, function ($item) {
                if(! isset($item['enabled']) || $item['enabled'] === false) {
                    return null;
                }

                return $item;
            });
        }

//        d($result);

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
     * @param $id
     * @return mixed|null
     */
    public function getDomainDataById($id)
    {
        $items = $this->getDomainsData();

        $result = $items[$id] ?? null;

        if(!isset($result['language_iso']) && isset($items[$id]['country'])) {
            $path = Yii::getAlias('@common') . '/config/locale/' . $items[$id]['country'] . '/params.php';
            if (file_exists($path)) {
                $data = require $path;
                if (is_array($data)) {
                    $result = array_merge($result, $data);
                }
            }
        }

        return $result;
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
    public function setCookie(string $alias, $request = false)
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
     * Возвращает текущий альяс домена
     *
     * @return mixed|string
     */
    public function getCurrentDomainAlias()
    {
        $data = $this->getCurrentDomainData();

        return $data['alias'] ?? null;
    }
    
    /**
     * Возвращает текущий язык домена
     *
     * @param bool $reset
     * @return mixed|string
     */
    public function getCurrentDomainLocaleId()
    {
        $data = $this->getCurrentDomainData();
        $locale_id = $data['language_id'] ?? null;

        return $locale_id;
    }

    /**
     * Возвращает текущий язык домена
     *
     * @param bool $reset
     * @return mixed|string
     */
    public function getDomainLocaleId($domain_id)
    {
        $data = $this->getDomainDataById($domain_id);
        $locale_id = $data['language_id'] ?? null;

        return $locale_id;
    }


    /**
     * Возвращает массив доменов сгруппированных по языкам
     * [
     *      locale_id_1 => [
     *          'used_domain_id' => domain_id_1, // domain_id которое редактируется
     *          'on_domains' => [  // домены на которых используется
     *                              [
     *                                   'domain_id' => 3,
     *                                   'country_image' => "{"id":7218,"path":"/static/08/55/Russia.svg","size":352,"height":null,"width":null}",
     *                                   'country_caption' => "Россия",
     *                              ],
     *                              [
     *                                   'domain_id' => 3,
     *                                   'country_image' => "{"id":7218,"path":"/static/08/55/Russia.svg","size":352,"height":null,"width":null}",
     *                                   'country_caption' => "Россия",
     *                              ],
     *                      ]
     *      ],
     *
     * ]
     *
     * @return array
     * @throws Exception
     */
    public function getDomainsByLocales()
    {
        $result = [];
        $domainsData = $this->getDomainsData();
        $domainsData = \yii\helpers\ArrayHelper::index($domainsData, 'alias');
        $locales = Yii::$app->localeService->getAllByCondition(function (\concepture\yii2logic\db\ActiveQuery $query) {
            $query->orderBy('sort ASC, id ASC');
            $query->indexBy('locale');
        });
        foreach ($domainsData as $data) {
            if (! isset($data['languages'])) {
                $languages = [ $data['alias'] => $data['language']];
            }else{
                $languages = $data['languages'];
            }


            foreach ($languages as $dAlias => $lang) {
                $language_id = $locales[$lang]['id'] ?? null;
                $language_caption = $locales[$lang]['caption'] ?? null;

                $used_domain_id = $domainsData[$dAlias]['domain_id'] ?? null;

                $domain_id = $data['domain_id'] ?? null;
                $result[$language_id]['used_domain_id'] = $used_domain_id;
                $result[$language_id]['language_caption'] = $language_caption;
                $result[$language_id]['used_domain_id'] = $used_domain_id;
                $result[$language_id]['on_domains'][] = [
                    'domain_id' => $domain_id,
                    'country_image' => $data['country_image'],
                    'country' => $data['country'],
                    'country_caption' => $data['country_caption'],
                ];
            }
        }

        return $result;
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