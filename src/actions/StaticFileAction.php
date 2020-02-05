<?php

namespace concepture\yii2handbook\actions;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\traits\ServicesTrait;
use Yii;
use yii\base\Action;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use yii\web\Response;

/**
 * Class StaticFileAction
 * @package concepture\yii2handbook\actions
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileAction extends Action
{
    use ServicesTrait;

    /**
     * @inheritDoc
     */
    public function run($filename)
    {
        $this->controller->layout = null;
        $item = $this->staticFileService()->getFile($filename);
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $headers = $response->getHeaders();
        $headers->add('Content-Type', FileExtensionEnum::getContentType($item->extension));

        if(! $item) {
            return null;
        }

        return $item->content;
    }
}