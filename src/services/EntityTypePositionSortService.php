<?php

namespace concepture\yii2handbook\services;

use yii\db\ActiveQuery;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;

/**
 * Сервис для работы с
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionSortService extends Service
{
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
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
        $this->applyDomain($query);
    }
}