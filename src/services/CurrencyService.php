<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\traits\StatusTrait;
use Yii;

/**
 * Class CurrencyService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CurrencyService extends Service
{
    use StatusTrait;

    /**
     * Каталог 'code' => 'name (symbol)'
     * @return array
     * @throws \Exception
     */
    public function getCatalogWithCurrencySymbol()
    {
        return $this->catalog('id', function ($model) {
            return "{$model->name} ({$model->symbol_native})";
        });
    }
}
