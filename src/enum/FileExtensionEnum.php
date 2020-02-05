<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class FileExtensionEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class FileExtensionEnum extends Enum
{
    const XML = "xml";
    const TXT = "txt";

    public static function getContentTypes()
    {
        return [
            static::XML => 'text/xml',
            static::TXT => 'text/plain',
        ];
    }

    public static function labels()
    {
        return [
            self::XML => Yii::t('handbook', "xml"),
            self::TXT => Yii::t('handbook', "Текстовый"),
        ];
    }

    public static function getContentType($extension)
    {
        $types = static::getContentTypes();
        return $types[$extension] ?? 'text/plain';
    }
}