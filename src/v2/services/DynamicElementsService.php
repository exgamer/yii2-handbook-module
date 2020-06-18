<?php

namespace concepture\yii2handbook\v2\services;

use Yii;
use yii\base\Event;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\Model as YiiModel;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use concepture\yii2logic\helpers\DataLoadHelper;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\events\modify\AfterModifyEvent;
use concepture\yii2logic\services\events\modify\AfterBatchInsertEvent;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2handbook\v2\forms\DynamicElementsForm;
use concepture\yii2handbook\datasets\SeoData;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2handbook\v2\forms\DynamicElementsMultipleForm;
use concepture\yii2handbook\v2\enum\DynamicElementsNameEnum;
use concepture\yii2handbook\services\events\DynamicElementsGetEvent;
use concepture\yii2handbook\services\events\DynamicElementsEventInterface;
use concepture\yii2handbook\bundles\dynamic_elements\Bundle;
use concepture\yii2handbook\v2\dto\DynamicElementDto;

/**
 * Сервис динамическх элементов версия 2
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
     * @var array
     */
    private $routeData = [];

    /**
     * @var DynamicElementDto
     */
    private $dto;

    /**
     * @var array
     */
    private $callIds = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->view = \Yii::$app->getView();
        $this->dto = DynamicElementDto::instance();
        $this->setRouteData();
    }

    /**
     * @return DynamicElementsPropertyService
     */
    public function getPropertyService()
    {
        return Yii::$app->dynamicElementsPropertyService;
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
        parent::beforeCreate($form);
    }

    /**
     * @inheritDoc
     */
    protected function extendQuery(ActiveQuery $query) {}

    /**
     * Получение элементов
     *
     * @param int $type
     * @param string $name
     * @param string $value
     * @param string $caption
     * @param array $options
     *
     * @return string
     */
    public function getElement(int $type, string $name, string $caption, $options)
    {
        $this->dto->name = $name;
        $this->dto->caption = $caption;
        $this->applyOptions($options);
        $this->dto->key = "{$this->getCurrentRoutePrefix()}_{$this->dto->name}";
        $event = $this->elementsGetEvent($type, $this->dto->key, $this->dto->caption, $this->dto->value, $this->dto->general);
        $this->trigger(static::EVENT_BEFORE_GET_ELEMENT, $event);
        $reset = (! Yii::$app instanceof \yii\web\Application);
        $dataSet = $this->getDataSet(null, $reset);
        # если такого элемента нет, заполняем массив для записи в бд
        if(! property_exists($dataSet, strtolower($this->dto->key))) {
            # todo: убрал отлов ошибок
            $this->writeItems[$this->dto->key] = [
                'route' => $this->getCurrentRoute(),
                'route_params' => $this->getCurrentRouteParams(),
                'domain_id' => $this->domainService()->getCurrentDomainId(),
                'type' => $type,
                'name' => $this->dto->key,
                'value' => $this->dto->value,
                'caption' => $this->dto->caption,
                'general' => $this->dto->general,
                'multi_domain' => $this->dto->multi_domain
            ];

            $event->value = $this->dto->value;
            $this->trigger(static::EVENT_AFTER_GET_ELEMENT, $event);

            return $this->elementValue($event->value);
        }

        $value = ($dataSet->{strtolower($this->dto->key)} ?? null);
        $event->value = $this->dto->value = $value;
        $this->trigger(static::EVENT_AFTER_GET_ELEMENT, $event);
        if(isset($this->modelStack[$this->dto->key])) {
            $id = $this->modelStack[$this->dto->key]['id'];
            $this->callStack[$this->dto->key] = $id;
            $this->callIds[] = $id;
        }

        return $this->elementValue();
    }

    /**
     * Установка настроек элемента
     *
     * @param array $options
     */
    private function applyOptions($options)
    {
        $this->dto->value = $options['value'] ?? '';
        $this->dto->general = $options['general'] ?? false;
        $this->dto->no_control = $options['no_control'] ?? false;
        $this->dto->multi_domain = $options['multi_domain'] ?? true;
    }

    /**
     * Возвращает значение элемента
     *
     * @return string|null
     */
    private function elementValue()
    {
        # если это мета теги или title не возвращаем ничего, проставяться автоматически в методе apply
        if(in_array($this->dto->name, DynamicElementsNameEnum::metaValues())) {
            return null;
        }

        if($this->dto->no_control) {
            return $this->dto->value;
        }

        return $this->dynamicElementsService()->getManageControl();
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
            $title = $data->{strtolower($this->getCurrentRoutePrefix()) . "_title"} ?? null;
            $this->title = ( $data->seo_title ?? $title ?? $this->view->title);
        }

        if($model) {
            $this->title = $data->seo_title ?? $model->{$titleAttribute};
        }

        $description = $data->{strtolower($this->getCurrentRoutePrefix()) . "_description"} ?? null;
        $this->description = $data->seo_description ?? $description ?? null;
        $keywords = $data->{strtolower($this->getCurrentRoutePrefix()) . "_keywords"} ?? null;
        $this->keywords = $data->seo_keywords ?? $keywords ?? null;

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
    private function getDataSet($model = null, $reset = false)
    {
        static $dataSet;

        if($dataSet && ! $model && ! $reset) {
            return $dataSet;
        }

        if($reset || ! $dataSet) {
            $dataSet = new SeoData();
            $items = $this->getCurrentElements($reset);
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
    public function getCurrentElements($reset = false)
    {
        static $items;

        if($items && ! $reset) {
            return $items;
        }

        $items = $this->getAllByCondition(function(ActiveQuery $query) {
            $query->andWhere([
                'OR',
                ['route_hash' => $this->getCurrentRouteHash()],
                ['general' => 1]
            ]);
            $query->orderBy('general', 'route_hash');
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
        $domain_id = $form->domain_id;
        $attributes = $form->getAttributes();
        foreach ($attributes as $key => $value) {
            if($key === 'ids') {
                $ids = $value;

                continue;
            }

            if($key === 'domain_id') {
                continue;
            }

            $formData[] = [$form->ids[$index] ,$domain_id, $value];
            $index ++;
        }
        # находим исходные элементы и проверяем изменялись они или нет
        $items = $this->getAllByCondition(function( ActiveQuery $query ) use ($ids, $domain_id) {
            $query->addSelect(['id', 'p.value']);
            $query->andWhere(['id' => $ids]);
            $query->applyPropertyUniqueValue($domain_id);
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

        return $this->getPropertyService()->batchInsert(['entity_id', 'domain_id', 'value'], $insertData);
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
        $this->getDb()->transaction(function(Connection $db) {
            $items = $this->writeItems;
            if (! $items) {
                return null;
            }

            $records = [];
            foreach ($items as $item) {
                $form = new DynamicElementsForm();
                $form->load($item, '');
                if (!$form->validate()) {
                    Yii::warning($form->getErrors());
                    throw new Exception('Validation failed');
                }

                $result = $this->create($form);
                if (!$result) {
                    Yii::warning($form->getErrors());
                    throw new Exception('Dynamic element save failed');
                }

                $records[] = [
                    'id' => $result->id,
                    'value' => $result->value,
                    'multi_domain' => $result->multi_domain
                ];
            }

            $domains = $this->domainService()->getAllByCondition(function (ActiveQuery $query) {
                $query->andWhere([
                    'status' => StatusEnum::ACTIVE
                ]);
                $query->andWhere(['!=', 'id', $this->domainService()->getCurrentDomainId()]);
            });

            if ($records && $domains) {
                $fileds = ['entity_id', 'domain_id', 'value'];
                $rows = [];
                foreach ($domains as $domain) {
                    foreach ($records as $record) {
                        if(! $record['multi_domain']) {
                            continue;
                        }

                        $rows[] = [$record['id'], $domain->id, $record['value']];
                    }
                }

                if ($rows) {
                    $this->getPropertyService()->batchInsert($fileds, $rows);
                }
            }
        });
    }

    /**
     * Возвращает элемент управления элементом
     */
    public function getManageControl()
    {
        $id = null;
        if(isset($this->modelStack[$this->dto->key])) {
            $id = $this->modelStack[$this->dto->key]['id'];
        }

        if(! $id || ! $this->canManage()) {
            return $this->dto->value;
        }

        $interactiveMode = $this->getInteractiveMode();

        $class = 'yii2-handbook-dynamic-elements-manage-control ' . ($interactiveMode ? 'yii2-handbook-dynamic-elements-interactive-mode' : null);
        if($this->dto->general) {
            $class = "{$class} general";
        }

        if($this->dto->multi_domain === false) {
            $class = "{$class} multi_domain";
        }

        return Html::tag(
            'span',
            $this->dto->value,
            [
                'class' => $class,
                'data-url' => $this->getUpdateUrl($id),
                'data-title' => $this->dto->caption,
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
        $totalCount = count($this->callIds);
        foreach ($this->callIds as $id) {
            $ids[] = $id;
        }

        $this->callIds = [];
        echo $this->view->render('@concepture/yii2handbook/v2/views/dynamic-elements/_manage_panel', [
            'url' => $this->getUpdateUrl($ids),
            'count' => $totalCount,
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
     * @deprecated
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
     * Возвращает хэш текущего адреса страницы
     *
     * @return string
     */
    public function getCurrentRouteHash()
    {
        if($this->currentUrlHash) {

            return $this->currentUrlHash;
        }

        return md5($this->getCurrentRoute());
    }

    /**
     * Установка хэша текущего урла страницы
     *
     * @param string $hash
     */
    public function setCurrentRouteHash($hash)
    {
        $this->currentUrlHash = $hash;
    }

    /**
     * Возвращает текущий роут
     *
     * @return string
     */
    private function getCurrentRoute($reset = false)
    {
        return $this->routeData['value'];
    }

    /**
     * Возвращает параметры текущего роута
     *
     * @return string
     */
    private function getCurrentRouteParams()
    {
        return Json::encode($this->routeData['params']);
    }

    /**
     * Возвращает префикс ключа
     *
     * @param boolean $general
     *
     * @return string
     */
    private function getCurrentRoutePrefix()
    {
        if($this->dto->general) {
            return 'GENERAL';
        }

        return strtoupper($this->routeData['prefix']);
    }

    /**
     * Установка данных роута
     */
    private function setRouteData()
    {
        $controller = Yii::$app->controller;
        if(! $controller) {
            return;
        }
        
        $prefix = str_replace('-', '_', "{$controller->id}_{$controller->action->id}");
        $value = "{$controller->id}/{$controller->action->id}";
        $this->routeData = [
            'prefix' => $prefix,
            'value' => $value,
        ];
        if(property_exists($controller, 'actionParams')) {
            $this->routeData['params'] = $controller->actionParams;
        }
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
        $domain_id = $this->domainService()->getCurrentDomainId();

        if(is_array($id)) {
            $url = [
                'admin/handbook/dynamic-elements/update-multiple',
                'ids' => implode(',', $id),
                'domain_id' => $domain_id
            ];
        } else {
            $url = [
                'admin/handbook/dynamic-elements/update',
                'id' => $id,
                'domain_id' => $domain_id
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