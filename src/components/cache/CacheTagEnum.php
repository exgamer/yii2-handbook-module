<?php

namespace concepture\yii2handbook\components\cache;

use concepture\yii2logic\enum\Enum;

/**
 * Перечисление тэгов кэша
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class CacheTagEnum extends Enum
{
    # один динамический элемент dynamic_element:id
    const DYNAMIC_ELEMENT = 'dynamic_element:';
    # один элемент сортировки
    const ENTITY_TYPE_POSTITON = 'enityt_type_position:';
}