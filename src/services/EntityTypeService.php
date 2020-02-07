<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\traits\StatusTrait;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class LocaleService
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleService extends Service
{
    use StatusTrait;

    /**
     * Возвращает ID текущей локали приложения
     *
     * @param bool $reset
     * @return int|array
     */
    public function getCurrentLocaleId($reset = false)
    {
        static $result;

        if($result && ! $reset) {
            return $result;
        }

        /**
         * Если есть параметр DomainMap тащим язык из него
         * 
         */
        if (! empty(Yii::$app->domainService->getDomainMap())){
            $domainMap = Yii::$app->domainService->getDomainMap();
            $domainMap = ArrayHelper::index($domainMap, 'alias');
            $currentDomain = Yii::$app->domainService->getCurrentDomain();
            if (empty($currentDomain)){
                throw new \Exception("curernt domain not found");
            }
            $locale = $domainMap[$currentDomain->alias]['locale'] ?? null;
            if (! $locale){
                throw new \Exception("curerent domain locale unknown");
            }
        }else{
            $locale = Yii::$app->language;
        }

        if (
            Yii::$app->has('request')
            && Yii::$app->getRequest() instanceof \yii\web\Request
            && Yii::$app->getRequest()->getQueryParam('_locale')
        ) {
            $locale = Yii::$app->getRequest()->getQueryParam('_locale');
        }

        if (is_array($locale)){
            return $locale;
        }

        $result =  LocaleConverter::key($locale);

        return $result;
    }
}
