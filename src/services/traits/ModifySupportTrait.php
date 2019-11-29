<?php
namespace concepture\yii2handbook\services\traits;

use Yii;
use concepture\yii2logic\forms\Model;

/**
 * Trait HandbookSupportTrait
 * @package concepture\yii2handbook\traits
 */
trait ModifySupportTrait
{
    /**
     * Устанавливает текущий домен
     * @param Model $model
     */
    protected function setCurrentDomain(Model $model)
    {
        $model->domain_id = Yii::$app->domainService->getCurrentDomainId();
    }

    /**
     * Устанавливает текущую локаль
     * @param Model $model
     */
    protected function setCurrentLocale(Model $model)
    {
        $model->locale = Yii::$app->localeService->getCurrentLocaleId();
    }
}

