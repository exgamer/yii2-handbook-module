<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use yii\base\Event;
use yii\web\Application;
use yii\web\UrlManager as YiiUrlManager;
use yii\web\UrlNormalizer;
use yii\helpers\Url;
use concepture\yii2handbook\components\routing\DomainUrlRule;
use yii\web\UrlRule;
use yii\web\UrlRuleInterface;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DomainUrlManager extends YiiUrlManager
{
    /**
     * @var bool
     */
    public $enablePrettyUrl = true;

    /**
     * @var bool
     */
    public $showScriptName = false;

    /**
     * @var string
     */
    public $suffix = '';

    /**
     * @var array
     */
    public $trailingSlashDeniedStatuses = [
        301,
        302,
        404,
        500,
    ];

    /**
     * @var array
     */
    public $normalizer = [
        'class' => 'yii\web\UrlNormalizer',
        'normalizeTrailingSlash' => false
    ];

    /**
     * @var array
     */
    public $ruleConfig = [
        'class' => DomainUrlRule::class
    ];

    /**
     * @var DomainUrlRule
     */
    private $currentRule;

    /**
     * @return DomainUrlRule
     */
    public function getCurrentRule()
    {
        return $this->currentRule;
    }

    /**
     * ========================================================================
     * Этот блок копирует методы из родителя
     * только для того чтобы можно было переопределить метод parent::createUrl
     * чтобы добавить туда блок для реинита правила
     */


    private $_ruleCache;

    /**
     * Store rule (e.g. [[UrlRule]]) to internal cache.
     * @param $cacheKey
     * @param UrlRuleInterface $rule
     * @since 2.0.8
     */
    protected function setRuleToCache($cacheKey, UrlRuleInterface $rule)
    {
        $this->_ruleCache[$cacheKey][] = $rule;
    }

    /**
     * Get URL from internal cache if exists.
     * @param string $cacheKey generated cache key to store data.
     * @param string $route the route (e.g. `site/index`).
     * @param array $params rule params.
     * @return bool|string the created URL
     * @see createUrl()
     * @since 2.0.8
     */
    protected function getUrlFromCache($cacheKey, $route, $params)
    {
        if (!empty($this->_ruleCache[$cacheKey])) {
            foreach ($this->_ruleCache[$cacheKey] as $rule) {
                /* @var $rule UrlRule */
                if (($url = $rule->createUrl($this, $route, $params)) !== false) {
                    return $url;
                }
            }
        } else {
            $this->_ruleCache[$cacheKey] = [];
        }

        return false;
    }

    /**
     * Копия родительского с учетом переключения доменов
     * для корректной генерации урл
     *
     * @param $params
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function createCustomUrl($params)
    {
        $params = (array) $params;
        $anchor = isset($params['#']) ? '#' . $params['#'] : '';
        unset($params['#'], $params[$this->routeParam]);

        $route = trim($params[0], '/');
        unset($params[0]);

        $baseUrl = $this->showScriptName || !$this->enablePrettyUrl ? $this->getScriptUrl() : $this->getBaseUrl();

        if ($this->enablePrettyUrl) {
            $cacheKey = $route . '?';
            foreach ($params as $key => $value) {
                if ($value !== null) {
                    $cacheKey .= $key . '&';
                }
            }

            $url = $this->getUrlFromCache($cacheKey, $route, $params);
            if ($url === false) {
                foreach ($this->rules as $rule) {
                    if (in_array($rule, $this->_ruleCache[$cacheKey], true)) {
                        // avoid redundant calls of `UrlRule::createUrl()` for rules checked in `getUrlFromCache()`
                        // @see https://github.com/yiisoft/yii2/issues/14094
                        continue;
                    }

                    /**
                     * От parent::createUrl отличается этим блоком для корректной генерации урл в админке
                     *
                     */
                    if (isset($rule->patterns) && $rule->patterns && (Yii::$app->domainService->getRealCurrentHost() != Yii::$app->domainService->getCurrentHost())){
                        $data = Yii::$app->domainService->getCurrentDomainData();
                        /**
                         * @TODO если в domainMap есть версия но нет паттерна в роутах тут выбьет ошибка
                         * и это так и должно быть не нужно тут ставить try/catch
                         */
                        if( $route == $rule->route) {
                            $rule->reinit($data['alias']);
                        }
                    }

                    $url = $rule->createUrl($this, $route, $params);
                    if ($this->canBeCached($rule)) {
                        $this->setRuleToCache($cacheKey, $rule);
                    }
                    if ($url !== false) {
                        break;
                    }
                }
            }

            if ($url !== false) {
                if (strpos($url, '://') !== false) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 8)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }

                    return $url . $baseUrl . $anchor;
                } elseif (strncmp($url, '//', 2) === 0) {
                    if ($baseUrl !== '' && ($pos = strpos($url, '/', 2)) !== false) {
                        return substr($url, 0, $pos) . $baseUrl . substr($url, $pos) . $anchor;
                    }

                    return $url . $baseUrl . $anchor;
                }

                $url = ltrim($url, '/');
                return "$baseUrl/{$url}{$anchor}";
            }

            if ($this->suffix !== null) {
                $route .= $this->suffix;
            }
            if (!empty($params) && ($query = http_build_query($params)) !== '') {
                $route .= '?' . $query;
            }

            $route = ltrim($route, '/');
            return "$baseUrl/{$route}{$anchor}";
        }

        $url = "$baseUrl?{$this->routeParam}=" . urlencode($route);
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
            $url .= '&' . $query;
        }

        return $url . $anchor;
    }

    /**
     *  Конец
     * =============================================================================
     */

    /**
     * @inheritDoc
     */
    public function createUrl($params)
    {
        # отрубаем для админки
        $admin = strpos($params[0], 'admin/') !== false;
        if(! $admin) {
            $this->suffix = '/';
        }

        $result = ltrim($this->createCustomUrl($params), '/');
        if(! $admin) {
            $this->suffix = '';
        }

        if($result == "") {
            return '/';
        }

        if(! $result) {
            throw new \Exception("Pattern for route {$params[0]} is not defined");
        }

        return str_replace(DomainUrlRule::PATTERN_SUFFIX, '', "/{$result}");
    }

    /**
     * @inheritDoc
     */
    public function parseRequest($request)
    {
        if ($this->enablePrettyUrl) {
            $this->registerRedirectEvent();
            /* @var $rule DomainUrlRule */
            foreach ($this->rules as $rule) {
                try {
                    $result = $rule->parseRequest($this, $request);
                } catch (\Exception $e) {
                    continue;
                }

                if (YII_DEBUG) {
                    Yii::debug([
                        'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
                        'match' => $result !== false,
                        'parent' => null,
                    ], __METHOD__);
                }
                if ($result !== false) {
                    $this->currentRule = $rule;

                    return $result;
                }
            }

            if ($this->enableStrictParsing) {
                return false;
            }

            Yii::debug('No matching URL rules. Using default URL parsing logic.', __METHOD__);

            $suffix = (string) $this->suffix;
            $pathInfo = $request->getPathInfo();
            $normalized = false;
            if ($this->normalizer !== false) {
                $pathInfo = $this->normalizer->normalizePathInfo($pathInfo, $suffix, $normalized);
            }

            if ($suffix !== '' && $pathInfo !== '') {
                $n = strlen($this->suffix);
                if (substr_compare($pathInfo, $this->suffix, -$n, $n) === 0) {
                    $pathInfo = substr($pathInfo, 0, -$n);
                    if ($pathInfo === '') {
                        // suffix alone is not allowed
                        return false;
                    }
                } else {
                    // suffix doesn't match
                    return false;
                }
            }

            if ($normalized) {
                // pathInfo was changed by normalizer - we need also normalize route
                return $this->normalizer->normalizeRoute([$pathInfo, []]);
            }

            return [$pathInfo, []];
        }

        Yii::debug('Pretty URL not enabled. Using default URL parsing logic.', __METHOD__);
        $route = $request->getQueryParam($this->routeParam, '');
        if (is_array($route)) {
            $route = '';
        }

        return [(string) $route, []];
    }

    /**
     * Регистрация cобытие перенаправления с адреса без слеша на адрес с слешем
     */
    private function registerRedirectEvent()
    {
        Event::on(Application::class, Application::EVENT_AFTER_REQUEST, function($event) {
            $app = $event->sender;
            $request = $app->getRequest();
            $url = $request->getUrl();
            $response = $app->getResponse();
            if(in_array($response->statusCode, $this->trailingSlashDeniedStatuses) ) {
                return true;
            }

            $pathInfo = $request->getPathInfo() ;
            $queryParams = trim(str_replace($pathInfo, null, $url), '/');
            $slash = substr($pathInfo, -1);
            if(
                $pathInfo !== ''
                && $slash !== '/'
                && $this->getCurrentRule() instanceof DomainUrlRule
                && $this->getCurrentRule()->normalizeTrailingSlash === true
            ) {
                $response->redirect(Url::to('/' . trim($pathInfo, '/') . '/') . $queryParams, UrlNormalizer::ACTION_REDIRECT_PERMANENT);
            }
        });
    }
}