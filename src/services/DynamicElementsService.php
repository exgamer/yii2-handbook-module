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
use concepture\yii2handbook\search\DynamicElementsSearch;
use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2handbook\forms\DynamicElementsMultipleForm;
use concepture\yii2handbook\enum\DynamicElementsEnum;

/**
 * Сервис динамическх элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsService extends Service
{
    const INTERACTIVE_MODE_SESSION = 'ineractive_mode';

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
     * @return \yii\web\Session
     */
    private function getSession()
    {
        return Yii::$app->session;
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
     * Получение элементов
     *
     * @param int $type
     * @param string $name
     * @param string $value
     * @param string $caption
     *
     * @return string
     */
    public function getElements(int $type, string $name, string $caption, $value = '', $is_general = false)
    {
        $dataSet = $this->getDataSet();
        $attribute = strtolower($name);
        # если такой настройки нет, записываем в массив для записи
        if(! isset($dataSet->{$attribute})) {
            $this->writeItems[$name] = [
                'url' => $this->getCurrentUrl($is_general),
                'url_md5_hash' => $this->getCurrentUlrHash($is_general),
                'domain_id' => $this->getDomainService()->getCurrentDomainId(),
                'locale' => $this->getLocaleService()->getCurrentLocaleId(),
                'type' => $type,
                'name' => $name,
                'value' => $value,
                'caption' => $caption,
                'is_general' => $is_general,
            ];

            return $value;
        }

        return $dataSet->{$attribute} ?? null;
    }

    /**
     * Установка элементов для страницы
     *
     * @param YiiModel $model
     */
    public function apply(YiiModel $model = null)
    {
        $data = $this->getDataSet($model);
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
     * Возвращает элементы для текущей страницы
     *
     * @param null $model
     * @return SeoData|mixed
     * @throws InvalidConfigException
     */
    public function getDataSet($model = null)
    {
        static $dataSet;

        if($dataSet && ! $model) {
            return $dataSet;
        }

        if(! $dataSet) {
            $dataSet = new SeoData();
            $items = $this->getElementsForCurrentUrl();
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
     * Возвращает элементы для текущей страницы и дефолтные с учетом языка приложения
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getElementsForCurrentUrl()
    {
        static $items;

        if($items) {
            return $items;
        }

        $items = $this->getAllByCondition(function(ActiveQuery $query) {
            $query->andWhere("url = '' OR url_md5_hash = :url_md5_hash",
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
            $query->andWhere([
                'OR',
                ['url_md5_hash' => $hash],
                ['url' => '']
            ]);
            $query->orderBy('url_md5_hash, id');
        });
    }

    /**
     * @param DynamicElementsSearch $searchModel
     * @return \yii\data\ActiveDataProvider
     */
    public function getDataProviderGroupByHash(DynamicElementsSearch $searchModel)
    {
        $condition = function(ActiveQuery $query) {
            $query->select(['url_md5_hash', 'url', 'count(id) as hash_count']);
            $query->groupBy('url_md5_hash, url');
        };

        return $this->getDataProvider([], [], $searchModel, null, $condition);
    }

    /**
     * Возвращает текущий адрес страницы
     *
     * @param bool $is_general
     *
     * @return string
     * @throws InvalidConfigException
     */
    private function getCurrentUrl($is_general = false)
    {
        static $result;

        if($is_general) {
            return '';
        }

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
     * @param bool $is_general
     *
     * @return string
     */
    public function getCurrentUlrHash($is_general = false)
    {
        if($is_general) {

            return md5('');
        }

        return md5($this->getCurrentUrl());
    }

    /**
     * Обновление значений пачкой
     *
     * @param DynamicElementsMultipleForm $form
     * @return mixed
     */
    public function updateMultiple(DynamicElementsMultipleForm $form)
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
     * Записывает новые элементы в базу
     *
     * @return |null
     */
    public function writeElements()
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
     * Возвращает элемент управления элементами
     *
     * @param string $name
     * @param string $caption
     * @param string $value
     * @param boolean $is_general
     */
    public function getManageControl($name, $caption, $value = '', $is_general = false)
    {
        # если это мета теги или title не возвращаем ничего, проставяться автоматически в методе apply
        if(in_array($name, DynamicElementsEnum::values())) {
            return null;
        }

        $interactiveMode = $this->getInteractiveMode();
        if( ! $this->canManage()) {
            return $value;
        }

        $class = 'yii2-handbook-dynamic-elements-manage-control ' . ($interactiveMode ? 'yii2-handbook-dynamic-elements-interactive-mode' : null);
        if($is_general) {
            $class = "{$class} general";
        }

        return Html::tag(
            'div',
            $value,
            [
                'class' => $class,
                'data-url' => $this->getUpdateUrl($name),
                'data-title' => $caption,
                'is_general' => $is_general,
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

        echo $this->view->render('@concepture/yii2handbook/views/dynamic-elements/include/manage_panel', [
            'url' => $this->getUpdateUrl(),
            'count' => count($this->existsItems),
            'interactiveMode' => $this->getInteractiveMode()
        ]);
    }

    /**
     * Признак интерактивного мода режима на внешней стороне
     *
     * @return mixed
     */
    public function getInteractiveMode()
    {
        $result = $this->getSession()->get(self::INTERACTIVE_MODE_SESSION, false);

        return ( $result === 'true' ? true : false );
    }

    /**
     * Установка значения интерактивного режима на внешней стороне
     *
     * @param bool $value
     */
    public function setInteractiveMode($value)
    {
        $this->getSession()->set(self::INTERACTIVE_MODE_SESSION, $value);
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
            'admin/handbook/dynamic-elements/update',
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