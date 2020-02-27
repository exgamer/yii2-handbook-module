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
     * Фиктивный суфикс для реализации закрывающегося / в конце адреса
     */
    const PATTERN_SUFFIX = '/?+';

    /**
     * @var array массив правил по доменам
     */
    public $patterns;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var bool
     */
    public $normalizeTrailingSlash = true;

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

        $result = $this->multiplePattern();
        if($result) {
            parent::init();
        } else {
            try {
                parent::init();
            } catch (\Exception $e) {}
        }
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
        if(! $this->origin_pattern) {
            return;
        }

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
            $this->addSuffix();

            return true;
        }

        if(! is_array($this->patterns)) {
            $this->pattern = $this->patterns;
        } else {
            if(! $domainMap) {
                $domainService = $this->getDomainService();
                $domainMap = $domainService->getDomainMap();
            }

            if (! Yii::$app instanceof \yii\web\Application) {
                $hostName = Yii::$app->domainService->getCurrentHost();
            }else{
                $hostName = $this->getRequest()->getHostName();
            }

            $alias = $domainMap[$hostName]['alias'] ?? null;
            $this->locale = $domainMap[$hostName]['locale'] ?? null;
            if(! $alias) {
                throw new InvalidConfigException("Domain alias is not found in domainMap.");
            }

            if(! isset($this->patterns[$alias])) {
                return false;
            }

            $this->setPatternByAlias($alias);
        }

        $this->addSuffix();

        return true;
    }

    /**
     * Добавление суфикса к правилу, для / в конце
     */
    private function addSuffix()
    {
        # главная страница
        if($this->pattern === '/') {
            return;
        }

        $this->pattern .= self::PATTERN_SUFFIX;
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

        $this->pattern = $this->patterns[$alias] ?? null;
    }
}