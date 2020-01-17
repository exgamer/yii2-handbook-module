<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use yii\web\UrlManager as YiiUrlManager;
use yii\web\UrlNormalizer;
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
    public $normalizer = [
        'class' => 'yii\web\UrlNormalizer',
        'action' => UrlNormalizer::ACTION_REDIRECT_TEMPORARY
    ];

    /**
     * @var array
     */
    public $ruleConfig = [
        'class' => DomainUrlRule::class
    ];

    /**
     * @var UrlRule
     */
    private $currentRule;

    /**
     * @return UrlRule
     */
    public function getCurrentRule()
    {
        return $this->currentRule;
    }

    /**
     * @inheritDoc
     */
    public function parseRequest($request)
    {
        if ($this->enablePrettyUrl) {
            /* @var $rule DomainUrlRule */
            foreach ($this->rules as $rule) {
                $result = $rule->parseRequest($this, $request);
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
}