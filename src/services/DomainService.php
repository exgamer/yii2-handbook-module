<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Domain;
use concepture\yii2logic\services\Service;
use yii\helpers\Url;
use Yii;

/**
 * Class DomainService
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainService extends Service
{
    /**
     * Возвращает карту доменов из параметров
     *
     * @return mixed|null
     */
    public function getDomainMap()
    {
        if (! isset(Yii::$app->params['yii2handbook'])){

            return null;
        }

        if (! isset(Yii::$app->params['yii2handbook']['domainMap'])){

            return null;
        }

        return Yii::$app->params['yii2handbook']['domainMap'];
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
        $domains = $this->catalog(false);
        $domains = array_flip($domains);

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