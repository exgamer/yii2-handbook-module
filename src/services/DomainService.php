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

        $domain = $this->getCurrentDomain();
        if(! $domain) {
            return null;
        }

        $result = $domain->id;

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
        $currentDomain = null;
        if (Yii::$app instanceof \yii\web\Application) {
            $currentDomain = Url::base(true);
        }
        $parsed = parse_url($currentDomain);
        $currentDomain = $parsed['scheme'] . "://" . $parsed['host'];
        $result = $this->getOneByCondition(['domain' => $currentDomain]);

        return $result;
    }
}