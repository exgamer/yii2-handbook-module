<?php

namespace concepture\yii2handbook\components\i18n;

use common\helpers\AppHelper;

/**
 * Class GettextMessageSource
 * @package common\i18n
 */
class GettextMessageSource extends \yii\i18n\GettextMessageSource
{
    /**
     * @param string $category
     * @param string $language
     * @return array
     */
    protected function loadMessages($category, $language)
    {
        return parent::loadMessages($category, $language);
    }
}