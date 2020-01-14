<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\db\ActiveQuery;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\Model as YiiModel;
use yii\base\InvalidConfigException;
use yii\web\Application;
use concepture\yii2handbook\search\SeoSettingsSearch;
use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2handbook\forms\SeoSettingsMultipleForm;
use concepture\yii2handbook\bundles\seosetting\Bundle;
use concepture\yii2handbook\enum\SeoSettingEnum;

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
     * @var array
     */
    private $existsItems = [];

    /**
     * @var array
     */
    private $writeItems = [];

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
     * @var string
     */
    private $text;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->view = \Yii::$app->getView();
        # todo : пока так, мб и не поменяется, только на фронте
        if(Yii::$app->has('request') && Yii::$app->request->baseUrl == '') {
            Bundle::register($this->view);
            Yii::$app->on(Application::EVENT_AFTER_REQUEST, [$this, 'writeSettings']);
            $this->view->on(View::EVENT_END_BODY, [$this, 'renderManagePanel']);
        }
    }

    /**
     * @return \concepture\yii2handbook\services\DomainService
     */
    private function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @return \concepture\yii2handbook\services\LocaleService
     */
    private function getLocaleService()
    {
        return Yii::$app->localeService;
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

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
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
     * Получение SEO настройки
     *
     * @param int $type
     * @param string $name
     * @param string $value
     * @param string $caption
     *
     * @return string
     */
    public function getSetting(int $type, string $name, string $value, string $caption)
    {
        $dataSet = $this->getSeoDataSet();
        $attribute = strtolower($name);
        # если такой настройки нет, записываем в массив для записи
        if(! isset($dataSet->{$attribute})) {
            $this->writeItems[$name] = [
                'url' => $this->getCurrentUrl(),
                'url_md5_hash' => $this->getCurrentUlrHash(),
                'domain_id' => $this->getDomainService()->getCurrentDomainId(),
                'locale' => $this->getLocaleService()->getCurrentLocaleId(),
                'type' => $type,
                'name' => $name,
                'value' => $value,
                'caption' => $caption,
            ];

            return $value;
        }

        return $dataSet->{$attribute} ?? null;
    }

    /**
     * Установка сео настроек для страницы
     *
     * @param YiiModel $model
     */
    public function apply(YiiModel $model = null)
    {
        $data = $this->getSeoDataSet($model);
        $this->title = ($data->seo_title ?? $data->title ?? $this->view->title);
        $this->description = $data->seo_description ?? $data->description ?? null;
        $this->keywords = $data->seo_keywords ?? $data->keywords ?? null;

        if(null !== $data->seo_h1) {
            $this->heading = $data->seo_h1;
        }

        if(null !== $data->seo_text) {
            $this->text = $data->seo_text;
        }
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
        static $dataSet;

        if($dataSet && ! $model) {
            return $dataSet;
        }

        if(! $dataSet) {
            $dataSet = new SeoData();
            $items = $this->getSettingsForCurrentUrl();
            foreach ($items as $item) {
                $dataSet->setVirtualAttribute($item->name, $item->value);
                $this->existsItems[$item->name] = $item->getAttributes();
            }
        }

        if ($model){
            $dataSet = DataLoadHelper::loadData($model, $dataSet, true);
        }

        return $dataSet;
    }

    /**
     * Возвращает настройки SEO для текущей страницы и дефолтные с учетом чзыка приложения
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getSettingsForCurrentUrl()
    {
        static $items;

        if($items) {
            return $items;
        }

        $items = $this->getAllByCondition(function(ActiveQuery $query) {
            $query->andWhere("url_md5_hash = :url_md5_hash OR url_md5_hash IS NULL",
                [
                    ':url_md5_hash' => $this->getCurrentUlrHash()
                ]
            );
            $query->orderBy('url');
        });

        return $items;
    }

    /**
     * @param string hash
     * @return \yii\data\ActiveDataProvider
     */
    public function getAllByHash(string $hash)
    {
        return $this->getAllByCondition(function(ActiveQuery $query) use($hash) {
            $query->andWhere(['url_md5_hash' => $hash]);
            $query->orderBy('id');
        });
    }

    /**
     * @param SeoSettingsSearch $searchModel
     * @return \yii\data\ActiveDataProvider
     */
    public function getDataProviderGroupByHash()
    {
        $condition = function(ActiveQuery $query) {
            $query->select(['*', 'count(id) as hash_count']);
            $query->groupBy('url_md5_hash');
            $query->orderBy('id DESC');
        };

        return $this->getDataProvider([], [], null, null, $condition);
    }

    /**
     * Обновление значений пачкой
     *
     * @param SeoSettingsMultipleForm $form
     * @return mixed
     */
    public function updateMultiple(SeoSettingsMultipleForm $form)
    {
        $index = 0;
        $data = [];
        $attributes = $form->getAttributes();
        foreach ($attributes as $key => $value) {
            if($key === 'ids') {
                continue;
            }

            $data[] = [$form->ids[$index] ,$value];
            $index++;
        }

        return $this->batchInsert(['id', 'value'], $data);
    }

    /**
     * Возвращает текущий адрес страницы
     *
     * @return string
     * @throws InvalidConfigException
     */
    private function getCurrentUrl()
    {
        static $result;
        if($result) {
            return $result;
        }

        $result = Yii::$app->getRequest()->getPathInfo();
        $result = trim($result, '/');
        # главная страница
        if (! $result){
            $result = "/";
        }

        return $result;
    }

    /**
     * Возвращает хэш текущего адреса страницы
     *
     * @return string
     */
    public function getCurrentUlrHash()
    {
        return md5($this->getCurrentUrl());
    }

    /**
     * Записывает новые настройки в базу
     *
     * @return |null
     */
    public function writeSettings()
    {
        $data = $this->writeItems;
        if(! $data) {
            return null;
        }

        $item = reset($data);
        $fields = array_keys($item);
        $this->batchInsert($fields, $data);
    }

    /**
     * Возвращает элемент управления настройкой
     *
     * @param $value
     */
    public function getManageControl($name, $value, $caption)
    {
        # если это мета теги или title не возвращаем ничего, проставяться автоматически в методе apply
        if(in_array($name, SeoSettingEnum::values())) {
            return null;
        }

        if( ! $this->canManage()) {
            return $value;
        }

        return Html::tag(
            'div',
            $value,
            [
                'class' => 'yii2-handbook-seo-manage-control',
                'data-url' => $this->getUpdateUrl($name),
                'data-title' => $caption
            ]
        );
    }

    /**
     * Панель управления
     */
    public function renderManagePanel()
    {
        if(! $this->canManage()) {
            return null;
        }

        echo $this->view->render('@concepture/yii2handbook/views/seo-settings/include/manage_panel', [
            'url' => $this->getUpdateUrl(),
            'count' => count($this->existsItems)
        ]);
    }

    /**
     * Ссылка на редактирование настроек
     *
     * @param string|null $anchor
     *
     * @return string
     */
    private function getUpdateUrl($anchor = null)
    {
        $url = [
            'admin/handbook/seo-settings/update',
            'hash' => $this->getCurrentUlrHash()
        ];
        if($anchor) {
            $url['#'] = $anchor;
        }

        return Url::to($url);
    }

    /**
     * Возвращает признак возможности управления настройками с внешней части
     *
     * @return bool
     */
    private function canManage()
    {
        static $result;

        if($result) {
            return $result;
        }
        # todo: пока не имеем RBAC
        $result = ( Yii::$app->getUser()->getIsGuest() ? false : true );

        return $result;
    }
}