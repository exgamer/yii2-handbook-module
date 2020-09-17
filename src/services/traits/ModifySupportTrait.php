<?php

namespace concepture\yii2handbook\services\traits;

use Yii;
use yii\base\Model;

/**
 * Trait HandbookSupportTrait
 * @package concepture\yii2handbook\traits
 */
trait ModifySupportTrait
{
    /**
     * Устанавливает текущий домен
     * @param Model $model
     * @param bool $ignoreSetted
     */
    protected function setCurrentDomain(Model $model, $ignoreSetted = true)
    {
        if (! $ignoreSetted){
            return;
        }

        if ($model->domain_id){
            return;
        }

        $model->domain_id = Yii::$app->domainService->getCurrentDomainId();
    }

    /**
     * @param Model $model
     * @param bool $ignoreSetted
     */
    protected function setCurrentDomainLocale(Model $model, $ignoreSetted = true)
    {
        if (! $ignoreSetted){
            return;
        }

        if ($model->locale_id){
            return;
        }

        $model->locale_id = Yii::$app->domainService->getCurrentDomainLocaleId();
    }

    /**
     * Устанавливает текущую локаль
     * @param Model $model
     * @param bool $ignoreSetted
     */
    protected function setCurrentLocale(Model $model, $ignoreSetted = true)
    {
        if (! $ignoreSetted){
            return;
        }

        if ($model->locale){
            return;
        }

        $model->locale = Yii::$app->localeService->getCurrentLocaleId();
    }
}

