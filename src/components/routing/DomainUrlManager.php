<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use yii\base\Event;
use yii\web\Application;
use yii\web\UrlManager as YiiUrlManager;
use yii\web\UrlNormalizer;
use yii\helpers\Url;
use concepture\yii2handbook\components\routing\DomainUrlRule;

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
    public $suffix = '/';

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
//        'normalizeTrailingSlash' => false
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
     * @inheritDoc
     */
    public function createUrl($params)
    {
        # отрубаем для админки
        $admin = strpos($params[0], 'admin/') !== false;
        if(! $admin) {
            $this->suffix = '/';
        }
        # todo: место узкое - если действие по умолчанию не index
        $defaultRoute = Yii::$app->defaultRoute . '/index';
        $result = ltrim(parent::createUrl($params), '/');
        if(! $admin) {
            $this->suffix = '';
        }

        if(trim($params[0], '/') === $defaultRoute) {
            return $result;
        }

        if(! $result) {
            throw new \Exception("Pattern for route {$params[0]} is not defined");
        }

//        return str_replace(DomainUrlRule::PATTERN_SUFFIX, '', "/{$result}");

        return "/{$result}";
    }

    /**
     * @inheritDoc
     */
    public function parseRequest($request)
    {
        if ($this->enablePrettyUrl) {
//            $this->registerRedirectEvent();
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
            if($pathInfo !== '' && $slash !== '/') {
                $response->redirect(Url::to('/' . trim($pathInfo, '/') . '/') . $queryParams, UrlNormalizer::ACTION_REDIRECT_PERMANENT);
            }
        });
    }
}