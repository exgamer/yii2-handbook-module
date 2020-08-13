<?php

namespace concepture\yii2handbook\components\routing;

use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;

/**
 * Сервис формирования альтарнативных адресов страниц по локалям
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangService extends Service
{
    use HandbookServicesTrait;

    /**
     * @var bool признак активности
     */
    private $_active = true;

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
    public function setDomainParamsByModelProps(ActiveRecord $model, array $attributes)
    {
        $traits = ClassHelper::getTraits($model);
        if (in_array(HasDomainPropertyTrait::class, $traits)) {
            $domainsData = $this->domainService()->getEnabledDomainData();
            $props = $model->getProperties()->all();
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
     * Получение элементов для формирования тэгов
     *
     * @return $array
     */
    private function getItems()
    {
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

                if(! isset($settings['enabled']) && $settings['enabled'] !== true) {
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
                $url = $urlManager->createUrl(ArrayHelper::merge([$route], $params));
                $result[$settings['hreflang']] =  "{$schema}://{$domain}{$url}";
            }
        } catch (\Exception $e) {

        }

        if($result) {
            $rule->reset();
        }

        return $result;
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