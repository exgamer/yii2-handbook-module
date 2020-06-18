<?php

namespace concepture\yii2handbook\services;

use concepture\yii2logic\services\Service;
use concepture\yii2handbook\forms\MessageMultipleForm;

/**
 * Сервис для работы с переводами по локале
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class MessageService extends Service
{
    /**
     * Обновление значений пачкой
     *
     * @param MessageMultipleForm $form
     *
     * @return mixed
     */
    public function updateMultiple(MessageMultipleForm $form)
    {
        $index = 0;
        $data = [];
        $attributes = $form->getAttributes();
        foreach ($attributes as $key => $value) {
            if(in_array($key, ['ids', 'languages', 'plurals'])) {
                continue;
            }

            $data[] = [$form->ids[$index], $form->languages[$index], $value];
            $index++;
        }

        $result = $this->batchInsert(['id', 'language', 'translation'], $data);

        return $result;
    }
}