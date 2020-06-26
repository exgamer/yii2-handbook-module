<?php

namespace concepture\yii2handbook\v2\console\controllers;

use concepture\yii2handbook\console\controllers\DynamicElementsCommand as V1;

/**
 * Консольные команды динамических элементов версия 2
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementsCommand extends V1
{
    /**
     * Удаление записей по роуту
     *
     * @param string $fromUrl
     * @param string $toUrl
     */
    public function actionDeleteByRoute($route)
    {
        try {
            $items = $this->getDynamicElementsService()->getAllByCondition(['route' => $route]);
            if(! $items) {
                $this->outputSuccess("Records is not found");

                return;
            }

            foreach ($items as $item) {
                $item->delete();
                $this->outputSuccess("Delete record: {$item->id}, by route: {$route} was successfully");
            }
        } catch (\Exception $e) {
            $this->outputDone($e->getMessage());
        }
    }
}