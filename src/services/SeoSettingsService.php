<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use Yii;
use yii\web\View;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;

/**
 * Class SeoSettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoSettingsService extends Service
{
    use HandbookServices;
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;

    /**
     * @var View
     */
    private $view;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $heading;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->view = \Yii::$app->getView();
    }

    /**
     * Установка сео настроек для страницы
     *
     * @param Model $model
     */
    public function apply(Model $model = null)
    {
        $data = $this->getSeoDataSet($model);
        if(null !== $data->seo_title) {
            $this->title = $data->seo_title;
        } else {
            $this->title = $this->view->title;
        }

        if(null !== $data->seo_description) {
            $this->description = $data->seo_description;
        }

        if(null !== $data->seo_keywords) {
            $this->keywords = $data->seo_keywords;
        }

        if(null !== $data->seo_h1) {
            $this->heading = $data->seo_h1;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        $this->setCurrentLocale($form);
    }

    /**
     * Метод для расширения find()
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * @param ActiveQuery $query
     * @see \concepture\yii2logic\services\Service::extendFindCondition()
     */
    protected function extendQuery(ActiveQuery $query)
    {
        $this->applyDomain($query);
        $this->applyLocale($query);
    }

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
        $current = trim($current, '/');
        $md5 = md5($current);

        return $this->getAllByCondition(function(ActiveQuery $query) use ($md5){
            $query->andWhere("url_md5_hash = :url_md5_hash OR url_md5_hash IS NULL",
                [
                    ':url_md5_hash' => $md5
                ]
            );
            $query->orderBy('url');
        });
    }
}
