<?php

namespace concepture\yii2handbook\twig;

use Yii;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\services\DomainService;

/**
 * Расширения twig для работы с доменами
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DomainExtension extends AbstractExtension
{
    /**
     * @return DomainService
     */
    private function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            # атрибут текущего домена приложения
            new TwigFunction('currentDomainAttribute', function($attribute = 'alias') {
                    $domain = $this->getDomainService()->getCurrentDomain();

                    return $domain->{$attribute} ?? null;
                }
            ),
        ];
    }
}