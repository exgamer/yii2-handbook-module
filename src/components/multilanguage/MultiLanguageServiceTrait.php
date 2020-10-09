<?php

namespace concepture\yii2handbook\components\multilanguage;

use Yii;

/**
 * Трейт для доступа к сервису языков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait MultiLanguageServiceTrait
{
    /**
     * @return MultiLanguageService
     */
    public function multiLanguageService()
    {
        return Yii::$app->multiLanguageService;
    }
}