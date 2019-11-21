<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;

/**
 * Class SeoSettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsService extends Service
{
    /**
     * Возвращает настройки SEO для текущей страницы и дефолтные с учетом чзыка приложения
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getSeoForCurrentUrl()
    {
        $current = Yii::$app->getRequest()->getPathInfo();
        $md5 = md5($current);

        return $this->getAllByCondition(function(ActiveQuery $query) use ($md5){
            $query->andWhere("(url_md5_hash IN (:url_md5_hash, NULL)",
                [
                    ':url_md5_hash' => $md5
                ]
            );
            $query->andWhere("(locale = :locale",
                [
                    ':locale' => Yii::$app->language
                ]
            );
        });
    }
}
