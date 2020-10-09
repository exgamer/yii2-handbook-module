<?php

namespace concepture\yii2handbook\components\multilanguage;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Расширение твига для мультиязычности
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MultiLanguageTwigExtension extends AbstractExtension
{
    use MultiLanguageServiceTrait;

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'multi_lang_current',
                function() {
                    return $this->multiLanguageService()->getCurrentLanguageParam();
                }
            ),
            new TwigFunction(
                'multi_lang_use_domain_lang',
                function(bool $value) {
                    return $this->multiLanguageService()->setUseDomainLanguage($value);
                }
            ),
            new TwigFunction(
                'multi_lang_is_use_domain_lang',
                function() {
                    return $this->multiLanguageService()->isUseDomainLanguage();
                }
            ),
        ];
    }
}