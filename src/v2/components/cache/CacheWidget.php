<?php

namespace concepture\yii2handbook\v2\components\cache;

use Yii;
use yii\base\InvalidConfigException;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\widgets\Widget;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;
use concepture\yii2handbook\v2\components\cache\CacheServiceTrait;
use concepture\yii2handbook\components\cache\CacheService;
use concepture\yii2handbook\components\cache\CacheTagEnum;

/**
 * Виджет с кешируемым контентом
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class CacheWidget extends Widget
{
    use CacheServiceTrait,
        HandbookServicesTrait;

    private $timer;

    /**
     * @var array
     */
    protected $dynamicElements = [];

    /**
     * @var array
     */
    protected $options = [
        'cache' => true, //признак кеширования контента
        'cache_ttl' => 0 //время кеширования, по умолчанию бесконечно
    ];

    /**
     * Возвращает контент виджета
     *
     * @return string
     */
    protected abstract function getContent();

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if($this->isCacheOption() && ! $this->getCacheKey()) {
            throw new InvalidConfigException('cache key must be set example - cache_key:');
        }
        
        $this->timer = pinba_timer_start(['group' => 'widget', 'name' => static::class]);
    }

    /**
     * @return bool|void
     */
    public function beforeRun()
    {
        if(Yii::$app instanceof \yii\web\Application) {
            $this->setOptions([
                'current_route_hash' => $this->dynamicElementsService()->getCurrentRouteHash()
            ]);

            $routeData = $this->dynamicElementsService()->getRouteData();
            if($routeData) {
                $this->setOptions([
                    'current_route_data' => $routeData
                ]);
            }
        }

        $this->setPropertiesOptions();

        return parent::beforeRun();
    }

    /**
     * @inheritDoc
     */
    public function afterRun($result)
    {
        $result =  parent::afterRun($result);
        pinba_timer_stop($this->timer);
        
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function run($cache = true)
    {
        parent::run();

        return $this->getResult($cache);
    }

    /**
     * Возвращает результат виджета для вывода
     *
     * @param boolean $cache - признак получение из кеша
     *
     * @return string HTML
     */
    private function getResult($cache = true)
    {
        $result = null;
        $cache_key = $this->getCacheKey() . $this->getOptionsHash();
        if($this->isCacheOption() && $cache) {
            $result = $this->cacheService()->get($cache_key);
        }

        if(! $result) {
            $this->dynamicElements = [];
            $this->dynamicElementsService()->clearCallStack();
            $result = $this->getContent();
            if(! $result) {
                $result = ' ';
            }

            $callStack = $this->dynamicElementsService()->getCallStack();
            if(count($callStack) > 0) {
                $this->dynamicElements = $callStack;
            }

            if($this->isCacheOption()) {
                $tags = $this->getCacheTags();
                if($this->dynamicElements) {
                    foreach ($this->dynamicElements as $key => $id) {
                        $tags[] = CacheTagEnum::DYNAMIC_ELEMENT . $id;
                    }
                }

                $this->cacheService()->tags($tags);
                $this->cacheService()->callback(CacheService::CALLBACK_TYPE_WIDGET, static::class, 'run', $this->options);
                $this->cacheService()->set($cache_key, $result, $this->getCacheTtlOption());
            }
        }

        return $result;
    }

    /**
     * Объединение опций виджета и переданных опций
     *
     * @param array
     */
    public function setOptions($options)
    {
        $this->options = $options + $this->options;
    }

    /**
     * Получение опций виджета
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Установка публичных свойств виджета в опции
     *
     * @throws \ReflectionException
     */
    private function setPropertiesOptions()
    {
        # проставляет публичные свойства в опции
        $public = ClassHelper::getProperties($this);
        $static = ClassHelper::getProperties($this, \ReflectionProperty::IS_STATIC);
        $properties = array_diff($public, $static);
        foreach ($properties as $property) {
            $name = $property->getName();
            $this->options[$name] = $this->{$name};
        }
    }

    /**
     * Генерация контента виджета
     */
    public function pregenerate()
    {
        $this->getResult(false);
    }

    /**
     * @return string опции виджета в формате JSON
     */
    protected function getOptionsJson() : string
    {
        $exclude = $this->excludeOptions();
        $options = $this->options;
        if($exclude) {
            foreach ($exclude as $item) {
                unset($options[$item]);
            }
        }

        if (! $options) {
            return '';
        }

        return json_encode($options);
    }

    /**
     * @return string хэш строка опций виджета
     */
    protected function getOptionsHash() : string
    {
        if (! $this->getOptionsJson()) {
            return '';
        }

        return md5($this->getOptionsJson());
    }

    /**
     * @return string название ключа в кеше
     */
    protected function getCacheKey() : string
    {
        return "";
    }

    /**
     * @return array массив тегов для кеша
     */
    protected  function getCacheTags() : array
    {
        return [];
    }

    /**
     * Ключи которые будут исключены из формирование хэша виджета
     * исключаем стандартный ключ 'cache' из хеширования
     *
     * @return array
     */
    protected function excludeOptions()
    {
        return ['cache', 'cache_ttl'];
    }

    /**
     * Название виджета
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getWidgetName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Уникальное название виджета
     *
     * @return string
     */
    protected function getUniqueName()
    {
        # todo обдумать надобность
        # return 'w_' . Uuid::uuid4();
    }

    /**
     * Возвращает признак кеширования контента
     *
     * @return boolean
     */
    protected function isCacheOption()
    {
        if(
            ! isset($this->options['cache'])
            || $this->options['cache'] != true
        ) {
            return false;
        }

        return true;
    }

    /**
     * Возвращает время жизни кеша
     *
     * @return integer
     */
    protected function getCacheTtlOption()
    {
        if(isset($this->options['cache_ttl'])) {
            return (int) $this->options['cache_ttl'];
        }

        return 0;
    }
}