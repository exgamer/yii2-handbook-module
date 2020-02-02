<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use yii\web\Request;
use yii\web\UrlRule as YiiUrlRule;
use yii\base\InvalidConfigException;
use concepture\yii2handbook\services\DomainService;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DomainUrlRule extends YiiUrlRule
{
    /**
     * @var array массив правил по доменам
     */
    public $patterns;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var string
     */
    private $origin_pattern;

    /**
     * @return DomainService
     */
    public function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return Yii::$app->request;
    }

    /**
     * @inheritDoc
     */
    public function init()
    {

        if(
            ! $this->patterns
            && ! $this->pattern
        ) {
            throw new InvalidConfigException('DomainUrlRule::pattern or DomainUrlRule::patterns must be set.');
        }

        $this->multiplePattern();

        parent::init();
    }

    /**
     * Установка патерна по альясу и переинициализация
     *
     * @param $alias
     *
     * @throws InvalidConfigException
     */
    public function reinit($alias)
    {
        $this->setPatternByAlias($alias);
        parent::init();
    }

    /**
     * Сброс на оригинальный паттерн
     *
     * @throws InvalidConfigException
     */
    public function reset()
    {
        $this->pattern = $this->origin_pattern;
        parent::init();
    }

    /**
     * @throws InvalidConfigException
     */
    private function multiplePattern()
    {
        static $domainMap;

        if(! $this->patterns) {
            return;
        }

        if(! is_array($this->patterns)) {
            $this->pattern = $this->patterns;
        } else {
            if(! $domainMap) {
                $domainService = $this->getDomainService();
                $domainMap = $domainService->getDomainMap();
            }

            $hostName = $this->getRequest()->getHostName();
            $alias = $domainMap[$hostName]['alias'] ?? null;
            $this->locale = $domainMap[$hostName]['locale'] ?? null;
            if(! $alias) {
                throw new InvalidConfigException("Domain alias is not found in domainMap.");
            }

            if(! isset($this->patterns[$alias])) {
                throw new InvalidConfigException("Route is not registered. ");
            }

            $this->setPatternByAlias($alias);
        }
    }

    /**
     * Установка паттерна по альясу версии приложения
     *
     * @param $alias
     */
    private function setPatternByAlias($alias)
    {
        if(! $this->origin_pattern) {
            $this->origin_pattern = $this->patterns[$alias];
        }

        $this->pattern = $this->patterns[$alias];
    }
}