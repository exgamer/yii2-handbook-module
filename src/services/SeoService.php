<?php

namespace concepture\yii2handbook\services;

use Yii;
use concepture\yii2logic\services\Service;

/**
 * Class SeoService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoService extends Service
{
    /**
     * добавить метатег noindex
     */
    public function noIndex()
    {
        \Yii::$app->view->registerMetaTag([
            'name' => 'robots',
            'content' => 'noindex'
        ]);
    }
}
