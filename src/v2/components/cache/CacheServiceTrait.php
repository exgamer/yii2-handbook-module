<?php

namespace concepture\yii2handbook\v2\components\cache;

use Yii;
use yii\base\InvalidConfigException;
use concepture\yii2handbook\components\cache\CacheService;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait CacheServiceTrait
{
    /**
     * @return CacheService
     */
    protected function cacheService()
    {
        $service = CacheService::COMPONENT_NAME;
        if(! Yii::$app->has($service)) {
            throw new InvalidConfigException("The component must have a name `{$service}`");
        }

        return Yii::$app->get($service);
    }
}