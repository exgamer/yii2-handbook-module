<?php

namespace concepture\yii2handbook\components\i18n;

use concepture\yii2handbook\enum\MessageCategoryEnum;

/**
 * Class GettextMessageSource
 * @package common\i18n
 */
class GettextMessageSource extends \yii\i18n\GettextMessageSource
{
    /**
     * @event CallTranslationEvent вызов элемента
     */
    const EVENT_CALL_TRANSLATION = 'callTranslation';

    /**
     * @param string $category
     * @param string $language
     * @return array
     */
    protected function loadMessages($category, $language)
    {
        return parent::loadMessages($category, $language);
    }

    /**
     * @inheritDoc
     */
    public function translate($category, $message, $language)
    {
        if(in_array($category, [MessageCategoryEnum::FRONTEND, MessageCategoryEnum::GENERAL])) {
            $event = new CallTranslationEvent([
                'category' => $category,
                'message' => $message,
                'language' => $language,
            ]);
            $this->trigger(self::EVENT_CALL_TRANSLATION, $event);
        }

        $result = parent::translate($category, $message, $language);

        return $result;
    }
}