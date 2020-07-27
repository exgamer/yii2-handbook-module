<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use yii\web\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\components\routing\HreflangService;

/**
 * Рсширение twig для формирования альтарнативных адресов страниц по локалям
 *
 * @deprecated функционал в SeoExtension
 * @todo на удаление
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangExtension extends AbstractExtension
{
    /**
     * @return HreflangService
     */
    private function getHreflangService()
    {
        return Yii::$app->hreflangService;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            # получение
            new TwigFunction(
                'hreflang',
                function() {
                    return $this->getHreflangService()->getTags() ?? '';
                },
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            )
        ];
    }
}