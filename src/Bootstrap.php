<?php
namespace concepture\yii2handbook;

use concepture\yii2logic\helpers\ApplicationHelper;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    //Метод, который вызывается автоматически при каждом запросе
    public function bootstrap($app)
    {
        //загружаем компоненты
        $components  = require_once __DIR__ . '/config/component.php';
        ApplicationHelper::setComponents($components);
    }
}