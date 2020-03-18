<?php

namespace concepture\yii2handbook\components\cache;

use Yii;
use concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker;

/**
 * Воркер для сброса кэша
 */
class CacheWorker extends BaseWorker
{
    use CacheServiceTrait;

    /**
     * @inheritDoc
     */
    public static function getTubeName()
    {
        return Yii::$app->get(CacheService::COMPONENT_NAME)->queueName;
    }

    /**
     * @inheritDoc
     */
    public function execute($data)
    {
        try {
            foreach($data as $row) {
                if(isset($row['tag'])) {
                    $this->getCacheService()->removeByTag($row['tag']);
                }

                if(isset($row['key'])) {
                    $this->getCacheService()->remove($row['key']);
                }
            }
        } catch (\Exception $e) {
            print $e->getMessage();
        }
    }
}