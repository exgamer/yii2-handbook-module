<?php

namespace concepture\yii2handbook\twig;

use Yii;
use yii\web\Application;
use yii\web\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\services\SeoSettingsService;
use concepture\yii2handbook\bundles\seosetting\Bundle;

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
        $view = Yii::$app->getView();
        Yii::$app->on(Application::EVENT_AFTER_REQUEST, [$this->getSeoSettingsService(), 'writeSettings']);
        $view->on(View::EVENT_END_BODY, [$this->getSeoSettingsService(), 'renderManagePanel']);
        Bundle::register($view);
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
                'seo_*',
                function($function) {
                    return $this->getSeoSettingsService()->{"get{$function}"}();
                }
            ),
            new TwigFunction(
                'seo_setting',
                function($type, $name, $caption, $value = null) {
                    $value = $this->getSeoSettingsService()->getSetting($type, $name, $caption, $value);

                    return $this->getSeoSettingsService()->getManageControl($name, $caption, $value);
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
            )
        ];
    }
}