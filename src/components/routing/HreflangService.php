<?php

namespace concepture\yii2handbook\components\routing;

use concepture\yii2handbook\components\multilanguage\MultiLanguageServiceTrait;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\models\interfaces\HasDomainPropertyInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;

/**
 * Сервис формирования альтернативных адресов страниц по локалям
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangService extends Service
{
    use HandbookServicesTrait,
        MultiLanguageServiceTrait;

    /**
     * @var bool признак активности
     */
    private $_active = true;
    /**
     * @var array
     */
    private $_items = [];
    /**
     * @var array дополнительные параметры по доменам
     *
     * "a" => [
     *  "name" => "abc"
     * ],
     * "b" => [
     * "name" => "bca"
     * ]
     */
    private $domainsRouteParams = [];
    /**
     * @var bool
     */
    private $isMultiLanguage = true;

    /**
     * @var array
     */
    private $onlyDomains = [];

    /**
     * Включение вывода элементов
     */
    public function enable()
    {
        $this->_active = true;
    }

    /**
     * Выключение вывода элементов
     */
    public function disable()
    {
        $this->_active = false;
    }

    /**
     * Отключение мультиязычности
     */
    public function disableMultiLanguage()
    {
        $this->isMultiLanguage = false;
    }

    /**
     * @param array $params
     */
    public function setDomainsRouteParams(array $params)
    {
        $this->domainsRouteParams = $params;
    }

    /**
     * @return array
     */
    public function getDomainsRouteParams()
    {
        return $this->domainsRouteParams;
    }

    /**
     * @param $aliaes
     */
    public function setOnlyDomains($aliaes)
    {
        $this->onlyDomains = $aliaes;
    }

    /**
     * Возвращает сформированные HTML тэги
     *
     * @return string|null
     * @throws HreflangServiceException
     */
    public function getTags()
    {
        if(! $this->_active) {
            return null;
        }

        $items = $this->getItems();
        if(! $items) {
            return null;
        }

        $tags = [];
        foreach ($items as $hreflang =>  $link) {
            $tags[] = Html::tag('link', null, ['rel' => 'alternate', 'href' => $link, 'hreflang' => $hreflang]);
        }
        if(count($tags) <= 1) {
            return null;
        }

        return implode('', $tags);
    }

    /**
     * Установка параметров домена по Моделе с пропертями
     *
     * @param ActiveRecord $model
     *
     * @param array $attributes ['param' => 'property_attribute']
     *
     * @throws HreflangServiceException
     */
    public function setDomainParamsByModelProps(ActiveRecord $model, array $attributes, \Closure $condition = null)
    {
        $traits = ClassHelper::getTraits($model);
        if (in_array(HasDomainPropertyTrait::class, $traits) ||
            $model instanceof HasDomainPropertyInterface
        ) {
            $domainsData = $this->domainService()->getEnabledDomainData();
            $query = $model->getProperties();
            if($condition) {
                call_user_func_array($condition, [$query]);
            }

            $props = $query->indexBy('domain_id')->all();
            if($props) {
                $params = [];
                foreach ($props as $domain_id => $property) {
                    if(! isset($domainsData[$domain_id])) {
                        continue;
                    }

                    $alias = $domainsData[$domain_id]['alias'];
                    $params[$alias] = [];
                    $link = &$params[$alias];
                    $class = get_class($model);
                    foreach ($attributes as $param => $attribute) {
                        if(! $property->hasAttribute($attribute)) {
                            throw new HreflangServiceException("Class `{$class}` doesn't have property `{$attribute}` is not defined");
                        }

                        $link[$param] = $property->{$attribute};
                    }
                }

                $this->setDomainsRouteParams($params);
            }
        }
    }

    /**
     * Установка параметров домена по переданным данным
     *
     * @param array $data [domain_id => ['a' => 'b', 'c' => 'd']]
     * @param array $attributes
     */
    public function setDomainParamsByData(array $data, array $attributes)
    {
        # если меньше двух записей ничего не выводим
        if(count($data) < 2) {
            $this->disable();

            return;
        }

        $domainsData = $this->domainService()->getEnabledDomainData();
        $keys = array_flip($attributes);
        $params = [];
        foreach ($data as $domain_id => $item) {
            if(! isset($domainsData[$domain_id])) {
                continue;
            }

            $domainData = $domainsData[$domain_id];
            $intersect = array_intersect_key($item, $keys);
            if(! $intersect) {
                Yii::warning("Hreflang service " . __FUNCTION__ . " wrong attributes");
                continue;
            }

            $params[$domainData['alias']] = $intersect;
        }

        if($params) {
            $this->setDomainsRouteParams($params);
        }
    }

    /**
     * Установка элементов для формирования тэгов
     *
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
    }

    /**
     * Получение элементов для формирования тэгов
     *
     * @return $array
     */
    public function getItems()
    {
        if($this->_items) {
            return $this->_items;
        }

        $domainMap = Yii::$app->params['yii2handbook']['domainMap'] ?? null;
        if(! $domainMap) {
            throw new HreflangServiceException('domainMap is not defined.');
        }

        $result = [];
        $urlManager = Yii::$app->urlManager;
        $rule = $urlManager->getCurrentRule();
        if(! $rule) {
            return $result;
        }

        list($route, $params) = $rule->parseRequest($urlManager, Yii::$app->getRequest());
        try {
            foreach ($domainMap as $domain => $settings) {
                if(
                    ! isset($settings['hreflang'])
                    || (
                        $rule->patterns
                        && ! isset($rule->patterns[$settings['alias']])
                    )
                ) {
                    continue;
                }

                if(! isset($settings['enabled']) || $settings['enabled'] !== true) {
                    continue;
                }
                # конекртные домены
                if($this->onlyDomains && ! in_array($settings['alias'], $this->onlyDomains)) {
                    continue;
                }
                # установка переданных параметров для формирования урла
                $domainsParams = $this->getDomainsRouteParams();
                $domainParams = $domainsParams[$settings['alias']] ?? null;
                # если передали параметры домена, и значение не нашлось, нельзя выводить тэг
                if(count($domainsParams) > 0 && ! $domainParams) {
                    continue;
                }

                if(
                    is_array($rule->patterns)
                    && count($rule->patterns) > 1
                ) {
                    $rule->reinit($settings['alias']);
                }

                if($domainParams) {
                    $params = ArrayHelper::merge($params, $domainParams);
                }

                $schema = $settings['schema'] ?? 'https';
                $languageHelper = $this->multiLanguageService()->helperClass;
                $languageMap = $languageHelper::getLanguageMap();
                if(! $this->isMultiLanguage || ($this->isMultiLanguage && ! $languageMap)) {
                    $url = $urlManager->createUrl(ArrayHelper::merge([$route], $params));
                    $result[$settings['hreflang']] =  "{$schema}://{$domain}{$url}";
                } else {
                    foreach ($languageMap as $languageParam => $iso) {
                        unset($params['language']);
                        list(,$countryIso) = @explode('-', $settings['hreflang']);
                        list($languageIso,) = @explode('-', $iso);
                        if($languageIso && $countryIso) {
                            $params['language'] = $languageParam;
                            $url = $this->multiLanguageService()->isolateCurrentLanguage(function() use($urlManager, $route, $params) {
                                return $urlManager->createUrl(ArrayHelper::merge([$route], $params));
                            });
                            $result["{$languageIso}-{$countryIso}"] =  "{$schema}://{$domain}{$url}";
                        }
                    }
                }
            }
        } catch (\Exception $e) {

        }

        if($result) {
            $rule->reset();
        }

        $this->_items = $result;

        return $this->_items;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangServiceException extends \Exception
{

}