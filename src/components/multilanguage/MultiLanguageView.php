<?php

namespace concepture\yii2handbook\components\multilanguage;

use frontend\themes\components\View;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageView extends View
{
    use MultiLanguageServiceTrait;

    /**
     * Начало мультиязычного контента
     */
    public function beginMultiLanguageContent()
    {
        $this->multiLanguageService()->setUseDomainLanguage(false);
    }

    /**
     * Конец мультиязычного контента
     */
    public function endMultiLanguageContent()
    {
        $this->multiLanguageService()->setUseDomainLanguage(true);
    }
//
//    /**
//     * @inheritDoc
//     */
//    public function pushBreadcrumbs($value)
//    {
//        $result = $this->multiLanguageService()->isolateCurrentDomain(function() use($value) {
//            return parent::pushBreadcrumbs($value);
//        });
//
//        return $result;
//    }
}