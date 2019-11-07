<?php
namespace concepture\yii2handbook\traits;

use concepture\yii2handbook\services\DomainService;
use concepture\yii2handbook\services\EntityTypeService;
use concepture\yii2handbook\services\LocaleService;
use Yii;

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
     * @return EntityTypeService
     */
    public function entityTypeService()
    {
        return Yii::$app->entityTypeService;
    }
}

