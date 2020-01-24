<?php

namespace concepture\yii2handbook\traits;

use Yii;
use concepture\yii2handbook\services\CountryService;
use concepture\yii2handbook\services\CurrencyService;
use concepture\yii2handbook\services\DomainService;
use concepture\yii2handbook\services\EntityTypeService;
use concepture\yii2handbook\services\EntityTypePositionService;
use concepture\yii2handbook\services\LocaleService;
use concepture\yii2handbook\services\PaymentSystemService;
use concepture\yii2handbook\services\SettingsService;
use concepture\yii2handbook\services\TagsService;
use concepture\yii2handbook\services\DynamicElementsService;
use concepture\yii2handbook\services\EntityTypePositionSortService;

/**
 * Trait ServicesTrait
 * @package concepture\yii2handbook\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait ServicesTrait
{
    /**
     * @return DomainService
     */
    public function domainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @return LocaleService
     */
    public function localeService()
    {
        return Yii::$app->localeService;
    }

    /**
     * @return CountryService
     */
    public function countryService()
    {
        return Yii::$app->countryService;
    }

    /**
     * @return CurrencyService
     */
    public function currencyService()
    {
        return Yii::$app->currencyService;
    }

    /**
     * @return PaymentSystemService
     */
    public function paymentSystemService()
    {
        return Yii::$app->paymentSystemService;
    }

    /**
     * @return EntityTypeService
     */
    public function entityTypeService()
    {
        return Yii::$app->entityTypeService;
    }

    /**
     * @return EntityTypePositionService
     */
    public function entityTypePositionService()
    {
        return Yii::$app->entityTypePositionService;
    }

    /**
     * @return EntityTypePositionSortService
     */
    public function entityTypePositionSortService()
    {
        return Yii::$app->entityTypePositionSortService;
    }

    /**
     * @return SettingsService
     */
    public function settingsService()
    {
        return Yii::$app->settingsService;
    }

    /**
     * @return TagsService
     */
    public function tagsService()
    {
        return Yii::$app->tagsService;
    }

    /**
     * @deprecated
     * @todo удалить (для совместимости)
     */
    public function seoSettingsService()
    {
        return Yii::$app->dynamicElementsService;
    }

    /**
     * @return DynamicElementsService
     */
    public function dynamicElementsService()
    {
        return Yii::$app->dynamicElementsService;
    }
}

