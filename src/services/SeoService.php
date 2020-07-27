<?php

namespace concepture\yii2handbook\services;

use Yii;
use \yii\helpers\Url;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServiceTrait;
use concepture\yii2logic\helpers\UrlHelper;

/**
 * Class SeoService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoService extends Service
{
    use HandbookServiceTrait;

    /**
     * @return |\yii\web\View
     */
    private function getView()
    {
        return Yii::$app->getView();
    }

    /**
     * @return \yii\web\Request
     */
    private function getRequest()
    {
        return Yii::$app->getRequest();
    }

    /**
     * добавить метатег noindex
     */
    public function noIndex()
    {
        $this->getView()->registerMetaTag([
            'name' => 'robots',
            'content' => 'noindex'
        ]);
    }

    /**
     * Устанавливает линк тэг каноникал
     *
     * @param array $route
     * @param boolean $rewrite
     */
    public function canonical(array $route = [], $rewrite = false)
    {
        $currentUrl = $this->getRequest()->getAbsoluteUrl();
        if(! $route) {
            $params = \Yii::$app->controller->actionParams;
            $canonicalUrl = Url::canonical();
            if (isset($params['page'])) {
                $params[0] = \Yii::$app->controller->getRoute();
                unset($params['page']);
                $canonicalUrl = UrlHelper::getFrontendUrlManager()->createAbsoluteUrl($params);
            }
        } else {
            $canonicalUrl = Yii::$app->getUrlManager()->createAbsoluteUrl($route);
        }

        $linkTag = $this->getView()->linkTags['canonical'] ?? null;
        if(! $linkTag || ($linkTag && $rewrite)) {
            # если  каноничный урл не равен текущему - отключаем хрефланги
            if($canonicalUrl !== $currentUrl) {
                $this->hreflangService()->disable();
            }

            $this->getView()->registerLinkTag(
                [
                    'rel' => 'canonical',
                    'href' => $canonicalUrl,
                ],
                'canonical'
            );
        }
    }
}
