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
        if($fromUrl !== '/') {
            $from = trim($fromUrl, '/');
        } else {
            $from = '/';
        }

        if($toUrl !== '/') {
            $to = trim($toUrl, '/');
        } else {
            $to = '/';
        }

        $models = $this->getDynamicElementsService()->getAllByHash(md5($from), false);
        if(! $models) {
            return $this->outputDone("Items for url `{$fromUrl}` is not found");
        }

        foreach ($models as $model) {
            $form = new DynamicElementsForm();
            $form->setAttributes($model->attributes, false);
            if (method_exists($form, 'customizeForm')) {
                $form->customizeForm($model);
            }

            $form->url = $to;
            $form->url_md5_hash = md5($to);
            $this->getDynamicElementsService()->update($form, $model);
            $this->outputSuccess("Item `{$model->id}` successfully replaced.");
        }
    }
}