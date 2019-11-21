<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;

/**
 * Class SettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsService extends Service
{
    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @param ActiveQuery $query
     */
    public function extendCatalogTraitQuery(ActiveQuery $query)
    {
        $domainId = Yii::$app->domainService->getCurrentDomainId();
        $sql = "domain_id = :domain_id OR domain_id IS NULL";
        $query->andWhere($sql, [':domain_id' => $domainId]);
        $locale = LocaleConverter::key(Yii::$app->language);
        $sql = "locale = :locale OR locale IS NULL";
        $query->andWhere($sql, [':locale' => $locale]);
    }
}
