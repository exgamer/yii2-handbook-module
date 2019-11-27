<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;

/**
 * Class TagsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsService extends Service
{
    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @see \concepture\yii2logic\services\traits\CatalogTrait::extendCatalogTraitQuery
     * @param ActiveQuery $query
     */
    protected function extendCatalogTraitQuery(ActiveQuery $query)
    {
        $domainId = Yii::$app->domainService->getCurrentDomainId();
        $sql = "domain_id = :domain_id OR domain_id IS NULL";
        $query->andWhere($sql, [':domain_id' => $domainId]);
        $locale = LocaleConverter::key(Yii::$app->language);
        $sql = "locale = :locale OR locale IS NULL";
        $query->andWhere($sql, [':locale' => $locale]);
        $query->andWhere(['is_deleted' => IsDeletedEnum::NOT_DELETED]);
    }
}
