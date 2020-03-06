<?php

namespace concepture\yii2handbook\actions;

use concepture\yii2handbook\traits\ServicesTrait;
use Yii;
use yii\base\Action;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use yii\web\Response;
use yii\web\NotFoundHttpException;

/**
 * Class SitemapAction
 * @package concepture\yii2handbook\actions
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapAction extends Action
{
    use ServicesTrait;

    /**
     * @inheritDoc
     */
    public function run($filename)
    {
        $this->controller->layout = null;
        $item = $this->staticFileService()->getSitemapFile($filename);
        if(! $item) {
            throw  new NotFoundHttpException();
        }

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $headers = $response->getHeaders();
        $headers->add('Content-Type', 'text/xml');

        return $item->content;
    }
}