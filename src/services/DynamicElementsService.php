<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\base\Event;
use yii\db\ActiveQuery;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\Model as YiiModel;
use yii\base\InvalidConfigException;
use concepture\yii2handbook\search\DynamicElementsSearch;
use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\events\modify\AfterModifyEvent;
use concepture\yii2logic\services\events\modify\AfterBatchInsertEvent;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2handbook\forms\DynamicElementsMultipleForm;
use concepture\yii2handbook\enum\DynamicElementsEnum;
use concepture\yii2handbook\services\events\DynamicElementsGetEvent;
use concepture\yii2handbook\services\events\DynamicElementsEventInterface;
use concepture\yii2handbook\bundles\dynamic_elements\Bundle;


/**
 * Сервис динамическх элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsService extends Service implements DynamicElementsEventInterface
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
     * @var array стек вызова элементов
     */
    private $callStack = [];

    /**
     * @var array стек моделей concepture\yii2handbook\models\DynamicElements
     */
    private $modelStack = [];

    /**
     * @var string
     */
    private $currentUrlHash = null;

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
     * Возвращает стек вызова элементов
     *
     * @return array
     */
    public function getCallStack()
    {
        return $this->callStack;
    }

    /**
     * Очистка стека вызова
     */
    public function clearCallStack()
    {
        $this->callStack = [];
    }

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        $this->setCurrentLocale($form);
        parent::beforeCreate($form);
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
//        $this->applyLocale($query);
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
        $event = $this->elementsGetEvent($type, $name, $caption, $value, $is_general);
        $this->trigger(static::EVENT_BEFORE_GET_ELEMENT, $event);
        $reset = (! Yii::$app instanceof \yii\web\Application);
        $dataSet = $this->getDataSet(null, $reset);
        $attribute = strtolower($name);
        # если такого элемента нет, заполняем массив для записи в бд
        if(! isset($dataSet->{$attribute})) {
            try {
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
            } catch (\Exception $e) {}

            $event->value = $value;
            $this->trigger(static::EVENT_AFTER_GET_ELEMENT, $event);

            return $event->value;
        }

        $event->value = ( $dataSet->{$attribute} ?? null);
        $this->trigger(static::EVENT_AFTER_GET_ELEMENT, $event);
        if(isset($this->modelStack[$name])) {
            $this->callStack[$name] = $this->modelStack[$name]['id'];
        }

        return $event->value;
    }

    /**
     * Установка элементов для страницы
     *
     * @param YiiModel $model
     */
    public function apply(YiiModel $model = null, $titleAttribute = 'header')
    {
        $data = $this->getDataSet($model);
        $event = new Event();
        $event->data = $data;
        $this->trigger(static::EVENT_BEFORE_APPLY, $event);
        if(! $model && ! $this->title ) {
            $this->title = ( $data->seo_title ?? $data->title ?? $this->view->title);
        }

        if($model) {
            $this->title = $data->seo_title ?? $model->{$titleAttribute};
        }

        $this->description = $data->seo_description ?? $data->description ?? null;
        $this->keywords = $data->seo_keywords ?? $data->keywords ?? null;

        if(null !== $data->seo_h1) {
            $this->heading = $data->seo_h1;
        }

        if(null !== $data->seo_text) {
            $this->text = $data->seo_text;
        }

        $this->trigger(static::EVENT_AFTER_APPLY, $event);
    }

    /**
     * Возвращает элементы для текущей страницы
     *
     * @param null $model
     * @return SeoData|mixed
     * @throws InvalidConfigException
     */
    public function getDataSet($model = null, $reset = false)
    {
        static $dataSet;

        if($dataSet && ! $model && ! $reset) {
            return $dataSet;
        }

        if($reset || ! $dataSet) {
            $dataSet = new SeoData();
            $items = $this->getElementsForCurrentUrl($reset);
            foreach ($items as $item) {
                $dataSet->setVirtualAttribute($item->name, $item->value);
                $this->existsItems[$item->name] = $item->getAttributes();
                $this->modelStack[$item->name] = $item;
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
    public function getElementsForCurrentUrl($reset = false)
    {
        static $items;

        if($items && ! $reset) {
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
     * Получения всех записей по хэшу
     *
     * @param string $hash
     * @param boolean $is_general
     *
     * @return array
     */
    public function getAllByHash(string $hash, $is_general = true)
    {
        return $this->getAllByCondition(function(ActiveQuery $query) use($hash, $is_general) {
            $query->andWhere([
                'OR',
                ['url_md5_hash' => $hash],
                ['url' => '']
            ]);
            $query->andWhere(['is_general' => $is_general]);
            $query->orderBy('is_general', 'url_md5_hash, id');
        });
    }

    /**
     * @deprecated
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

        // TODO обсудить с Илюхой, нужно на списках с пагинацией брать одни и те же элементы
        if (array_key_exists('page', Yii::$app->getRequest()->getQueryParams())) {
            $result = Url::toRoute(Yii::$app->requestedRoute);
        }

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
        if($this->currentUrlHash) {

            return $this->currentUrlHash;
        }

        if($is_general) {

            return md5('');
        }

        return md5($this->getCurrentUrl());
    }

    /**
     * Установка хэша текущего урла страницы
     *
     * @param string $hash
     */
    public function setCurrentUlrHash($hash)
    {
        $this->currentUrlHash = $hash;
    }

    /**
     * Обновление значений пачкой
     *
     * @param DynamicElementsMultipleForm $form
     * @return mixed
     */
    public function updateMultiple(DynamicElementsMultipleForm $form)
    {
        $ids = [];
        $formData = [];
        $index = 0;
        $attributes = $form->getAttributes();
        foreach ($attributes as $key => $value) {
            if($key === 'ids') {
                $ids = $value;

                continue;
            }

            $formData[] = [$form->ids[$index] ,$value];
            $index ++;
        }
        # находим исходные элементы и проверяем изменялись они или нет
        $items = $this->getAllByCondition(function( ActiveQuery $query ) use ($ids) {
            $query->addSelect(['id', 'value']);
            $query->andWhere(['id' => $ids]);
            $query->asArray();
            $query->indexBy('id');
        });
        $insertData = [];
        foreach ($formData as $key => $data) {
            list($id, $value) = $data;
            if(
                ! isset($items[$id])
                || ($items[$id]['value'] === $value)
            ) {
                continue;
            }

            $insertData[$key] = $data;
        }

        if(! $insertData) {
            return true;
        }

        return $this->batchInsert(['id', 'value'], $insertData);
    }

    /**
     * @inheritDoc
     */
    public function afterBatchInsert($fields, $rows)
    {
        $this->trigger(static::EVENT_AFTER_BATCH_INSERT, new AfterBatchInsertEvent(['fields' => $fields, 'rows' => $rows]));
        $event = new AfterModifyEvent();
        $event->modifyData = $rows;
        $this->trigger(static::EVENT_AFTER_MODIFY, $event);
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

        $id = null;
        if(isset($this->modelStack[$name])) {
            $id = $this->modelStack[$name]['id'];
        }

        if(! $id || ! $this->canManage()) {
            return $value;
        }

        $interactiveMode = $this->getInteractiveMode();

        $class = 'yii2-handbook-dynamic-elements-manage-control ' . ($interactiveMode ? 'yii2-handbook-dynamic-elements-interactive-mode' : null);
        if($is_general) {
            $class = "{$class} general";
        }

        return Html::tag(
            'span',
            $value,
            [
                'class' => $class,
                'data-url' => $this->getUpdateUrl($id),
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

        Bundle::register(Yii::$app->getView());

        $ids = [];
        foreach ($this->modelStack as $model) {
            $ids[] = $model->id;
        }

        echo $this->view->render('@concepture/yii2handbook/views/dynamic-elements/include/manage_panel', [
            'url' => $this->getUpdateUrl($ids),
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
     * Возвращает признак возможности управления настройками с внешней части
     *
     * @return bool
     */
    public function canManage()
    {
        static $result;

        if(! Yii::$app instanceof \yii\web\Application) {
            return false;
        }

        if($result) {
            return $result;
        }

        $result = ( Yii::$app->getUser()->getIsGuest() ? false : true );

        return $result;
    }

    /**
     * Замена урла элемента
     *
     * @param string $fromUrl
     * @param string $toUrl
     * @return array
     * @throws \Exception
     */
    public function replacementUrl($fromUrl , $toUrl)
    {
        $result = [];
        if($fromUrl !== '/') {
            $from = trim($fromUrl, '/');
        } else {
            $from = '/';
        }

        if($toUrl !== '/') {
            $to = trim($toUrl, '/');
        } else {
            $to = '/';
        }

        $models = $this->getAllByHash(md5($from), false);
        if(! $models) {
            throw new \Exception("Elements for url `{$fromUrl}` is not found");
        }

        foreach ($models as $model) {
            $form = $this->getRelatedForm();
            $form->setAttributes($model->attributes, false);
            if (method_exists($form, 'customizeForm')) {
                $form->customizeForm($model);
            }

            $form->url = $to;
            $form->url_md5_hash = md5($to);
            $this->update($form, $model);
            $result[] = "Element `NAME: {$model->name}`, `ID: {$model->id}` successfully replaced.";
        }

        return $result;
    }

    /**
     * Ссылка на редактирование настроек
     *
     * @param integer|array $id
     *
     * @return string
     */
    private function getUpdateUrl($id)
    {
        $domain = $this->getDomainService()->getCurrentDomain();
        $alias = null;
        if( isset($domain)) {
            $alias = $domain->alias;
        }

        if(is_array($id)) {
            $url = [
                'admin/handbook/dynamic-elements/update-multiple',
                'ids' => implode(',', $id),
                'domainAlias' => $alias
            ];
        } else {
            $url = [
                'admin/handbook/dynamic-elements/update',
                'id' => $id,
                'domainAlias' => $alias
            ];
        }

        return Url::to($url);
    }

    /**
     * @param int $type
     * @param string $name
     * @param string $caption
     * @param string $value
     * @param bool $is_general
     * @return DynamicElementsGetEvent
     */
    private function elementsGetEvent($type, $name, $caption, $value, $is_general)
    {
        $event = new DynamicElementsGetEvent();
        $event->type = $type;
        $event->name = $name;
        $event->caption = $caption;
        $event->value = $value;
        $event->is_general = $is_general;

        return $event;
    }
}