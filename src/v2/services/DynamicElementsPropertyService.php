<?php

namespace concepture\yii2handbook\v2\services;

use yii\db\ActiveQuery;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait;


/**
 * Сервис домеенных атрибутов динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsPropertyService extends Service
{
    use ModifySupportTrait,
        ReadSupportTrait;

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query) {}
}