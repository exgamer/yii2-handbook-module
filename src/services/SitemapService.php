<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\forms\SitemapForm;
use concepture\yii2handbook\traits\ServicesTrait;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\UrlHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use concepture\yii2logic\enum\StatusEnum;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class SitemapService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapService extends Service
{
    use StatusTrait;
    use ServicesTrait;
    use HandbookModifySupportTrait;
    use HandbookReadSupportTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
    }

    /**
     * @inheritDoc
     */
    protected function extendQuery(ActiveQuery $query)
    {
        $this->applyDomain($query);
    }

    protected function beforeModelSave(Model $form, ActiveRecord $model, $is_new_record)
    {
        $form->last_modified_dt = date("Y-m-d H:i:s");
    }

    /**
     * Добавить в карту саита ссылку
     *
     * @param ActiveRecord $model
     * @param array $urlParamAttrs
     *
     * @return mixed
     */
    public function add($model, $controllerId = null, $urlParamAttrs = ['seo_name'])
    {
        $frontendUrlManager = UrlHelper::getFrontendUrlManager();
        $queryParams = [];
        foreach ($urlParamAttrs as $attribute){
            $queryParams[$attribute] = $model->{$attribute};
        }

        $serviceName = ClassHelper::getServiceName($model);
        $section = Yii::$app->{$serviceName}->getTableName();
        $className = ClassHelper::getShortClassName($model);
        if (! $controllerId) {
            $controllerId = Inflector::camel2id($className);
        }

        $urlParams = ArrayHelper::merge([$controllerId . '/view'], $queryParams);
        $location = $frontendUrlManager->createUrl($urlParams);
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $section], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $form = new SitemapForm();
        $form->entity_type_id = $entity_type->id;
        $form->entity_id = $model->id;
        $form->location = $location;
        $form->section = $section;

        return $this->create($form);
    }
}