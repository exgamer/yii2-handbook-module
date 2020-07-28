<?php

namespace concepture\yii2handbook\twig;

use Yii;
use concepture\yii2handbook\services\SeoBlockService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Расширения twig для работы с сео блоками
 *
 * @deprecated функционал в SeoExtension
 * @todo на удаление
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @return SeoBlockService
     */
    private function getSeoBlockService()
    {
        return Yii::$app->seoBlockService;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'seo_block',
                function($position) {

                    if(! $this->items) {
                        $this->items = $this->getSeoBlockService()->getItems();
                    }

                    return $this->items[$position] ?? null;
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
