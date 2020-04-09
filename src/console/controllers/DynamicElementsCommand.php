<?php

namespace concepture\yii2handbook\console\controllers;

use concepture\yii2handbook\forms\DynamicElementsForm;
use Yii;
use concepture\yii2logic\controllers\console\ConsoleCommand;
use concepture\yii2handbook\services\DynamicElementsService;

/**
 * Консольные команды динамических элементов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsCommand extends ConsoleCommand
{
    /**
     * @return DynamicElementsService
     */
    private function getDynamicElementsService()
    {
        return Yii::$app->dynamicElementsService;
    }

    /**
     * Замена урлов динамических элементов
     *
     * @param string $fromUrl
     * @param string $toUrl
     */
    public function actionReplacement($fromUrl , $toUrl)
    {
        try {
            $messages = $this->getDynamicElementsService()->replacementUrl($fromUrl, $toUrl);
            if(! $messages || ! is_array($messages)) {
                return;
            }

            foreach ($messages as $message) {
                $this->outputSuccess($message);
            }
        } catch (\Exception $e) {
            $this->outputDone($e->getMessage());
        }
    }
}