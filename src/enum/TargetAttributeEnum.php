<?php

namespace concepture\yii2handbook\enum;

use Yii;

/**
 * Класс перечисления для значений аттрибута target html ссылки
 *
 * Class StatusEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TargetAttributeEnum extends Enum
{
    const BLANK = "_blank";
    const SELF = "_self";
    const PARENT = "_parent";
    const TOP = "_top";

    public static function labels()
    {
        return [
            self::BLANK => Yii::t('handbook', "Загружает страницу в новое окно браузера."),
            self::SELF => Yii::t('handbook', "Загружает страницу в текущее окно."),
            self::PARENT => Yii::t('handbook', "Загружает страницу во фрейм-родитель, если фреймов нет, то это значение работает как _self."),
            self::TOP => Yii::t('handbook', "Отменяет все фреймы и загружает страницу в полном окне браузера, если фреймов нет, то это значение работает как _self. "),
        ];
    }
}
