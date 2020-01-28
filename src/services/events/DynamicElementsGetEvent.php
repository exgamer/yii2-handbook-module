<?php

namespace concepture\yii2handbook\services\events;

use yii\base\Event;

/**
 * События динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsGetEvent extends Event
{
    public $type;
    public $name;
    public $caption;
    public $value;
    public $is_general;
}