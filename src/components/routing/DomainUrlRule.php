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
     * @var array
     */
    public $domainPatterns;

    /**
     * @var string
     */
    public $language;

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
            ! $this->domainPatterns
            && ! $this->pattern
        ) {
            throw new InvalidConfigException('DomainUrlRule::pattern or DomainUrlRule::domainPatterns must be set.');
        }

        $this->setPattern();

        parent::init();
    }

    /**
     * @throws InvalidConfigException
     */
    private function setPattern()
    {
        static $domainMap;

        if(! $this->domainPatterns) {
            return;
        }

        if(! is_array($this->domainPatterns)) {
            $this->pattern = $this->domainPatterns;
        } else {
            if(! $domainMap) {
                $domainService = $this->getDomainService();
                $domainMap = $domainService->getDomainMap();
            }

            $hostName = $this->getRequest()->getHostName();
            $alias = $domainMap[$hostName]['alias'] ?? null;
            $this->language = $domainMap[$hostName]['language'] ?? null;
            if(! $alias) {
                throw new InvalidConfigException("Domain alias is not found in domainMap.");
            }

            if(! isset($this->domainPatterns[$alias])) {
                throw new InvalidConfigException("Route is not registered. ");
            }

            $this->pattern = $this->domainPatterns[$alias];
        }
    }
}