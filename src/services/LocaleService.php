<?php

namespace concepture\yii2handbook\services;

use Yii;
use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use yii\db\ActiveQuery;

/**
 * Class LocaleService
 * @package concepture\yii2handbook\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleService extends Service
{
    use StatusTrait;
    use \concepture\yii2handbook\services\traits\ModifySupportTrait;
    use \concepture\yii2handbook\services\traits\ReadSupportTrait;
    use \concepture\yii2logic\services\traits\LocalizedReadTrait;

    /** @var array */
    static $localesCatalog = [];

    /**
     * @return DomainService
     */
    protected function getDomainService()
    {
        return Yii::$app->domainService;
    }

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
         */
        $locale = Yii::$app->domainService->getLocaleByDomainMap();
        if (!$locale){
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

    /**
     * Ограничевает выборку локалей по языкам из домен мапы
     *
     * @return array
     */
    public function getByDomainMap()
    {
        $locales = $this->getDomainService()->getDomainMapAttributes('language');
        if($locales) {
            $condition = function (ActiveQuery $query) use($locales) {
                $query->andWhere(['locale' => $locales]);
                $query->orderBy(['id' => SORT_ASC]);
            };
        }

        return parent::catalog(null, null, $condition, false, true);
    }

    /**
     * @return array|mixed
     */
    public function getCatalogBySql()
    {
        if (static::$localesCatalog) {
            return static::$localesCatalog;
        }
        static::$localesCatalog = $this->queryAll('SELECT id, locale FROM locale', [], \PDO::FETCH_KEY_PAIR);
        return static::$localesCatalog ? static::$localesCatalog : [];
    }
}
