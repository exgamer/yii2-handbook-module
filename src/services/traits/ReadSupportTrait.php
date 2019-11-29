<?php
namespace concepture\yii2handbook\services\traits;

use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Trait HandbookSupportTrait
 * @package concepture\yii2handbook\traits
 */
trait ReadSupportTrait
{
    /**
     * Добавялет в запрос условие выборки где domain_id текущий или null
     *
     * @param ActiveQuery $query
     */
    protected function applyDomain(ActiveQuery $query)
    {
        $query->andWhere("domain_id = :domain_id OR domain_id IS NULL", [':domain_id' => Yii::$app->domainService->getCurrentDomainId()]);
    }

    /**
     * Добавялет в запрос условие выборки где локаль текущая
     *
     * @param ActiveQuery $query
     */
    protected function applyLocale(ActiveQuery $query)
    {
        $query->andWhere("locale = :locale", [':locale' => Yii::$app->localeService->getCurrentLocaleId()]);
    }
}

