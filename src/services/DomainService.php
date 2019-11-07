<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Domain;
use concepture\yii2logic\services\Service;
use yii\helpers\Url;

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
     * @return int|null
     */
    public function getCurrentDomainId()
    {
        $domain = $this->getCurrentDomain();
        if ($domain){
            return $domain->id;
        }

        return null;
    }

    /**
     * Возвращает запись текущего домена
     *
     * @return Domain
     */
    public function getCurrentDomain()
    {
        $currentDomain = Url::base(true);

        $domain = $this->getOneByCondition(['domain' => $currentDomain]);

        return $domain;
    }
}
