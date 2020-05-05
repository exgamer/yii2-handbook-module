<?php

namespace concepture\yii2handbook\components\queue\beanstalkd;

use concepture\yii2logic\enum\Enum;

/**
 * Класс для хранения констант
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class BaseTubeEnum extends Enum
{
    const COMMAND = 'command';
}