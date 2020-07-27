<?php

namespace concepture\yii2handbook\twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServiceTrait;

/**
 * Class SeoExtension
 *
 * Расширение для отрисовки SEO тэгов
 *
 * @package frontend\components\twig
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class SeoExtension extends AbstractExtension
{
    use HandbookServiceTrait;

    /**
     * @var array
     */
    private $blocks = [];

    /**
     * @inheritDoc
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'canonical',
                function() {
                    return $this->seoService()->canonical();
                } ,
                [ 'is_safe' => ['html'] ]
            ),
            new TwigFunction(
                'hreflangs',
                function() {
                    return $this->hreflangService()->getTags() ?? '';
                },
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
            new TwigFunction(
                'seo_block',
                function($position) {

                    if(! $this->blocks) {
                        $this->blocks = $this->seoBlockService()->getItems();
                    }

                    return $this->blocks[$position] ?? null;
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