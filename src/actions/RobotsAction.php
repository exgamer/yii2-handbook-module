<?php

namespace concepture\yii2handbook\actions;

use concepture\yii2handbook\traits\ServicesTrait;
use Yii;
use yii\base\Action;
use concepture\yii2handbook\services\RobotsService;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use yii\web\Response;

/**
 * Действия для отдачи индексного файла - robot.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RobotsAction extends Action
{
    use ServicesTrait;

    /**
     * @return RobotsService
     */
    protected function getRobotService()
    {
        return Yii::$app->robotsService;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->controller->layout = null;
        $item = $this->staticFileService()->getRobotsFile();
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_RAW;
        $headers = $response->getHeaders();
        $headers->add('Content-Type', 'text/plain');

        if(! $item) {
            return null;
        }

        return $item->content;
    }
}