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
     * Возвращает id текущего домена
     *
     * @param bool $reset
     *
     * @return int|null
     */
    public function getCurrentDomainId($reset = false)
    {
        static $result;

        if($result && ! $reset) {

            return $result;
        }

        $host = $this->getCurrentHost();
        if(! $host) {
            return null;
        }

        $domains = $this->catalog();
        $domains = array_flip($domains);

        return $this->getCurrentDomainIdFromCatalog($host, $domains);

//        if (! isset($domains[$host])){
//
//            return null;
//        }
//        d($domains);
//        $result = $domains[$host];

//        return $result;
    }

    /**
     * Возвращает ID переданного хоста из каталога
     *
     * @param string $host
     * @param array $domainsCatalog
     * @return integer|null
     */
    protected function getCurrentDomainIdFromCatalog($host, $domainsCatalog)
    {
        if (! isset($domainsCatalog[$host])){

            return null;
        }

        return $domainsCatalog[$host];
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
        if (Yii::$app instanceof \yii\web\Application) {
            $currentDomain = Url::base(true);
        }
        $parsed = parse_url($currentDomain);

        $result = $parsed['scheme'] . "://" . $parsed['host'];

        return $result;
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

        $currentDomain = $this->getCurrentHost();
        $result = $this->getOneByCondition(['domain' => $currentDomain]);

        return $result;
    }
}