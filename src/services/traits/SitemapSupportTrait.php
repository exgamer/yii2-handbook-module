<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2logic\enum\StatusEnum;
use Yii;

/**
 * 
 * Trait ReadSupportTrait
 * @package concepture\yii2handbook\services\traits
 */
trait SitemapSupportTrait
{
    /**
     * Обновление карты саита
     *
     * @param $model
     * @param null $controllerId
     * @param array $urlParamAttrs
     * @param bool $forceDelete
     * @return mixed
     */
    public function sitemapRefresh($model, $controllerId = null, $urlParamAttrs = ['seo_name'], $forceDelete = false)
    {
        if ($forceDelete || $model->status != StatusEnum::ACTIVE){
            return Yii::$app->sitemapService->remove($model);;
        }

        return Yii::$app->sitemapService->refresh($model, $controllerId, $urlParamAttrs );
    }
}