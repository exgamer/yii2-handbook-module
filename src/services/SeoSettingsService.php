<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use Yii;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;

/**
 * Class SeoSettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsService extends Service
{
    use HandbookServices;

    /**
     * Возвращает настройки SEO для текущей страницы
     *
     * @param null $model
     * @return SeoData|mixed
     * @throws InvalidConfigException
     */
    public function getSeoDataSet($model = null)
    {
        $defaultSeoSetting = null;
        $pageCustomSeoSetting =null;
        $dataSet = new SeoData();
        $defaultSeoSettings = $this->getSeoForCurrentUrl();
        foreach ($defaultSeoSettings as $seoSetting){
            if (empty($seoSetting->url)){
                $defaultSeoSetting = $seoSetting;
                continue;
            }

            $pageCustomSeoSetting = $seoSetting;
        }

        if ($defaultSeoSetting){
            $dataSet = DataLoadHelper::loadData($defaultSeoSetting, $dataSet, true);
        }

        if ($model){
            $dataSet = DataLoadHelper::loadData($model, $dataSet, true);
        }

        if ($pageCustomSeoSetting){
            $dataSet = DataLoadHelper::loadData($pageCustomSeoSetting, $dataSet, true);
        }

        return $dataSet;
    }

    /**
     * Возвращает настройки SEO для текущей страницы и дефолтные с учетом чзыка приложения
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getSeoForCurrentUrl()
    {
        $current = Yii::$app->getRequest()->getPathInfo();
        $md5 = md5($current);

        return $this->getAllByCondition(function(ActiveQuery $query) use ($md5){
            $query->andWhere("url_md5_hash = :url_md5_hash OR url_md5_hash IS NULL",
                [
                    ':url_md5_hash' => $md5
                ]
            );
            $query->andWhere("locale = :locale",
                [
                    ':locale' => $this->localeService()->getCurrentLocaleId()
                ]
            );
            $query->orderBy('url');
        });
    }
}
