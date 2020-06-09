<?php

namespace concepture\yii2handbook\v2\twig;

use Yii;
use yii\web\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;

/**
 * Расширения twig для динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsExtension extends AbstractExtension
{
    use HandbookServicesTrait;

    /**
     * @var array
     */
    private $constants = [];

    public function __construct()
    {
        $view = Yii::$app->getView();
        $view->on(View::EVENT_BEGIN_PAGE, function() {
            return $this->dynamicElementsService()->apply();
        });
        $view->on(View::EVENT_END_BODY, function() {
            return $this->dynamicElementsService()->writeElements();
        });
        $view->on(View::EVENT_END_BODY, [$this->dynamicElementsService(), 'renderManagePanel']);
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
                    return $this->dynamicElementsService()->{"get{$function}"}();
                }
            ),
            new TwigFunction(
                'de',
                function($type, $name, $caption, $value = '', $is_general = false, $no_controls = false) {
                    $value = $this->dynamicElementsService()->getElements($type, $name, $caption, $value, $is_general);
                    if ($no_controls){
                        return $value;
                    }

                    return $this->dynamicElementsService()->getManageControl($name, $caption, $value, $is_general);
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
                    $namespace = "concepture\\yii2handbook\\v2\\enum\\{$class}";
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