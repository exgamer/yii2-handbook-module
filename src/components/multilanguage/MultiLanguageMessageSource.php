<?php

namespace concepture\yii2handbook\components\multilanguage;

use common\helpers\AppHelper;
use concepture\yii2handbook\components\i18n\GettextMessageSource as Base;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageMessageSource extends Base
{
    use MultiLanguageServiceTrait;

    /**
     * @var array
     */
    private $_languages = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->_languages['domain'] = AppHelper::getLanguage() . '-' . AppHelper::getCountry();
        $helperClass = $this->multiLanguageService()->helperClass;
        $current_iso = $helperClass::getCurrentIsoCode();
        list($language_iso, $country_iso) = $helperClass::parseIsoCode($current_iso);
        if($language_iso && $country_iso) {
            $this->_languages['current'] = "{$language_iso}-{$country_iso}";
        }
    }

    /**
     * @inheritDoc
     */
    protected function translateMessage($category, $message, $language)
    {
        if($this->multiLanguageService()->isUseDomainLanguage()) {
            if($language !== $this->_languages['domain']) {
                $language = $this->_languages['domain'];
            }
        } else {
            if(isset($this->_languages['current'])) {
                $language = $this->_languages['current'];
            }
        }

        return parent::translateMessage($category, $message, $language);
    }
}