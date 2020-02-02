<?php

namespace concepture\yii2handbook\services\events;

/**
 * Интерфейс событий динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
interface DynamicElementsEventInterface
{
    const EVENT_BEFORE_APPLY = 'beforeApply';

    const EVENT_AFTER_APPLY = 'afterApply';

    const EVENT_BEFORE_GET_ELEMENT = 'beforeElement';

    const EVENT_AFTER_GET_ELEMENT = 'afterElement';
}