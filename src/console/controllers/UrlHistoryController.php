<?php
namespace concepture\yii2handbook\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class UrlHistoryController extends Controller
{
    public function actionReGenerate()
    {
        Yii::$app->urlHistoryService->regenerate();
    }
}