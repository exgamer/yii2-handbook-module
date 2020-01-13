<?php

namespace concepture\yii2handbook\twig;

use concepture\yii2handbook\bundles\seosetting\Bundle;
use Yii;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\services\SeoSettingsService;

/**
 * Расширения twig для SEO настроек
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoSettingsExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $constants = [];

    public function __construct()
    {
        Bundle::register(Yii::$app->getView());
    }

    /**
     * @return SeoSettingsService
     */
    private function getSeoSettingsService()
    {
        return Yii::$app->seoSettingsService;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            # получение
            new TwigFunction(
                'seo_entity_*',
                function($function) {
                    return $this->getSeoSettingsService()->{"get{$function}"}();
                }
            ),
            new TwigFunction(
                'seo_setting',
                function($type, $name, $value, $caption) {
                    $value = $this->getSeoSettingsService()->getSetting($type, $name, $value, $caption);

                    return $this->getSeoSettingsService()->getManageControl($name, $value, $caption);
                },
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
            new TwigFunction(
                'seo_constant',
                function($value) {
                    list($class, $constant) = explode('::', $value);
                    $namespace = "concepture\\yii2handbook\\enum\\{$class}";
                    $hash = md5($namespace);
                    if(! isset($this->constants[$hash])) {
                        $reflection = new \ReflectionClass($namespace);
                        $constants = $reflection->getConstants();
                        $this->constants[$hash] = $constants;
                    } else {
                        $constants = $this->constants[$hash];
                    }

                    return $constants[$constant] ?? null;
                }
            ),
            new TwigFunction(
                'seo_manage_panel',
                function() {
                    return $this->getSeoSettingsService()->getManagePanel();
                },
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
        ];
    }
}