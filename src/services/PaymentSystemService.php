<?php

namespace concepture\yii2handbook\services;

use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2logic\services\traits\LocalizedReadTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;

/**
 * Class PaymentSystemService
 *
 * Сервис платежных систем
 *
 * @package concepture\yii2handbook\services
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class PaymentSystemService extends Service
{
    use StatusTrait;
    use ReadSupportTrait;
    use LocalizedReadTrait;
    use ModifySupportTrait;

    /**
     * @inheritDoc
     */
    public function catalog($from = null, $to = null, $condition = null, $excludeDefault = false, $resetModels = true)
    {
        return parent::catalog($from, $to, $condition, $excludeDefault, $resetModels);
    }
}

/**
 * Class PaymentSystemServiceException
 *
 * @package common\services
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class PaymentSystemServiceException extends \Exception {}
