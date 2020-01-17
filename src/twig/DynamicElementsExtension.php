<?php

namespace concepture\yii2handbook\twig;

use Yii;
use yii\web\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\services\DynamicElementsService;
use concepture\yii2handbook\bundles\dynamic_elements\Bundle;

/**
 * Расширения twig для динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsExtension extends AbstractExtension
{
    /**
     * @var array
     */
    private $constants = [];

    public function __construct()
    {
        $view = Yii::$app->getView();
        Bundle::register($view);
        $view->on(View::EVENT_BEFORE_RENDER, function() {
            return $this->getDynamicElementsService()->apply();
        });
        $view->on(View::EVENT_END_BODY, [$this->getDynamicElementsService(), 'renderManagePanel']);
        $view->on(View::EVENT_AFTER_RENDER, function() {
            return $this->getDynamicElementsService()->writeElements();
        });
    }

    /**
     * @return DynamicElementsService
     */
    private function getDynamicElementsService()
    {
        return Yii::$app->dynamicElementsService;
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
                    return $this->getDynamicElementsService()->{"get{$function}"}();
                }
            ),
            new TwigFunction(
                'de',
                function($type, $name, $caption, $value = '', $is_general = false) {
                    $value = $this->getDynamicElementsService()->getElements($type, $name, $caption, $value, $is_general);

                    return $this->getDynamicElementsService()->getManageControl($name, $caption, $value, $is_general);
                },
                [
                    'is_safe' => [
                        'html'
                    ]
                ]
            ),
            new TwigFunction(
                'de_constant',
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