<?php
namespace concepture\yii2handbook\console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class SitemapController extends Controller
{
    public function actionGenerate()
    {
        Yii::$app->sitemapService->generate();
    }

    public function actionReGenerate()
    {
        Yii::$app->sitemapService->regenerate();
    }
}