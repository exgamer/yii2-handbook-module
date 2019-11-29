<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;

/**
 * Class SettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsService extends Service
{
    use HandbookServices;

    /**
     * @see \concepture\yii2logic\services\Service::extendFindCondition()
     * @return array
     */
    protected function extendFindCondition()
    {
        return [
            ["domain_id = :domain_id OR domain_id IS NULL", [':domain_id' => $this->domainService()->getCurrentDomainId()]],
            ["locale = :locale OR locale IS NULL", [':domain_id' => $this->localeService()->getCurrentLocaleId()]],
        ];
    }
}
