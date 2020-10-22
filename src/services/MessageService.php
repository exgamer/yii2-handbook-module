<?php

namespace concepture\yii2handbook\services;

use concepture\yii2logic\services\Service;
use concepture\yii2handbook\forms\MessageMultipleForm;
use Yii;

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
            if(in_array($key, ['ids', 'languages', 'plurals', 'originText', 'isNewRecord'])) {
                continue;
            }

            $language = $form->languages[$index];
            if(strpos($language, '-') !== false) {
                list($languageIso, $countryIso) = explode('-', $language);
            }

            $countryId = Yii::$app->countryService->catalogKey($countryIso, 'id', 'iso');
            // вырезаем все переводы для стран к которым нет доступа
            if (! Yii::$app->user->hasDomainAccessByCountry($countryId)) {
                $index++;
                continue;
            }

            $data[] = [$form->ids[$index], $language, $value];
            $index++;
        }

        $result = $this->batchInsert(['id', 'language', 'translation'], $data);

        return $result;
    }
}