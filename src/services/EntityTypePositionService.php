<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2logic\services\traits\StatusTrait;

/**
 * Сервис для работы с
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionService extends Service
{
    use HandbookServices;
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;
    use StatusTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
//        $this->setCurrentDomain($form);
    }

    /**
     * Метод для расширения find()
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * @param ActiveQuery $query
     * @see \concepture\yii2logic\services\Service::extendFindCondition()
     */
    protected function extendQuery(ActiveQuery $query)
    {
//        $this->applyDomain($query);
    }

    /**
     * @inheritDoc
     *
     * @param bool $cache
     */
    public function getOneByCondition($condition = null, $cache = false)
    {
        if( ! Yii::$app->has('cache') || !$cache) {
            return parent::getOneByCondition($condition);
        }

        return $this->getDb()->cache(function ($condition) {
            return parent::getOneByCondition($condition);
        });
    }
}