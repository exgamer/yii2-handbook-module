<?php
namespace concepture\yii2handbook\console\controllers;

use yii\console\Controller;
use yii\helpers\Console;

class CommandController extends Controller
{
    public function actionIndex($message = 'hello world from module')
    {
        echo $message . "\n";
    }
}