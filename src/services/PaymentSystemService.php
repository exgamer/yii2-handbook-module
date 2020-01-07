<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\traits\StatusTrait;
use Yii;

/**
 * Class PaymentSystemService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PaymentSystemService extends Service
{
    use StatusTrait;
}
