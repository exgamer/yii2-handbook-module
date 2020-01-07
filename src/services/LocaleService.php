<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\traits\StatusTrait;
use Yii;

/**
 * Class LocaleService
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleService extends Service
{
    use StatusTrait;

    public $cache = true;

    /**
     * Возвращает ID текущей локали приложения
     *
     * @param bool $reset
     * @return int
     */
    public function getCurrentLocaleId($reset = false)
    {
        static $result;

        if($result && ! $reset) {
            return $result;
        }

        $result =  LocaleConverter::key(Yii::$app->language);

        return $result;
    }
}
