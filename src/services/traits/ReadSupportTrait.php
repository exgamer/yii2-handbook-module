<?php
namespace concepture\yii2handbook\services\traits;

use Yii;
use concepture\yii2handbook\traits\ServicesTrait;
use yii\db\ActiveQuery;
use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Trait HandbookSupportTrait
 * @package concepture\yii2handbook\traits
 */
trait ReadSupportTrait
{
    use ServicesTrait;

    /**
     * Добавялет в запрос условие выборки где domain_id текущий или null
     *
     * @param ActiveQuery $query
     */
    protected function applyDomain(ActiveQuery $query)
    {
        $query->andWhere("domain_id = :domain_id OR domain_id IS NULL", [':domain_id' => $this->domainService()->getCurrentDomainId()]);
    }

    /**
     * Добавялет в запрос условие выборки где локаль текущая
     *
     * @param ActiveQuery $query
     */
    protected function applyLocale(ActiveQuery $query)
    {
        $query->andWhere("locale = :locale", [':locale' => $this->localeService()->getCurrentLocaleId()]);
    }

    /**
     * Добавялет в запрос условие выборки где запись не удалена
     *
     * @param ActiveQuery $query
     */
    protected function applyNotDeleted(ActiveQuery $query)
    {
        $query->andWhere(['is_deleted' => IsDeletedEnum::NOT_DELETED]);
    }
}

