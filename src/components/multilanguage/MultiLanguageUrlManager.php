<?php

namespace concepture\yii2handbook\components\multilanguage;

use concepture\yii2handbook\components\routing\DomainUrlManager;
use yii\base\InvalidConfigException;

/**
 * Мультиязычный менеджер
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageUrlManager extends DomainUrlManager
{
    use MultiLanguageServiceTrait;

    /**
     * @inheritDoc
     */
    public function createUrl($params)
    {
        $helperClass = $this->multiLanguageService()->helperClass;
        $language_map = $helperClass::getLanguageMap();
        if(! $this->multiLanguageService()->isUseDomainLanguage() && $language_map) {
            $current_iso = $helperClass::getCurrentIsoCode();
            $language_iso = $helperClass::getParamByIsoCode($current_iso);
            if(! $language_iso) {
                throw new InvalidConfigException("Language iso code is not correctly");
            }

            if(isset($params['language'])) {
                $language_iso = $params['language'];
            } else {
                $params['language'] = $language_iso;
            }

            if($language_iso === MultiLanguageHelper::getDefaultLanguageIsoCode()) {
                unset($params['language']);
            }
        }

        return parent::createUrl($params);
    }
}