<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\forms\SitemapForm;
use concepture\yii2logic\db\ActiveQuery;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use Exception;
use Yii;

/**
 * Trait SitemapModifyTrait
 * @package concepture\yii2handbook\services\traits
 */
trait SitemapModifyTrait
{
    /**
     * Возвращает запись для конкретной модели сущности
     *
     * @param $entity_type
     * @param $model
     * @return mixed
     */
    public function getCurrentSitemap($entity_type, $model)
    {
        return $this->getOneByCondition(function (ActiveQuery $query) use ($entity_type, $model) {
            if ($model->hasAttribute('domain_id')) {
                $query->andWhere([
                    'domain_id' => $model->domain_id,
                ]);
            }

            $query->andWhere([
                'entity_type_id' => $entity_type->id,
                'entity_id' => $model->id,
            ]);
        });
    }

    /**
     * Добавить в карту саита ссылку
     *
     * @param ActiveRecord $model
     * @param string $controllerId
     * @param array $urlParamAttrs
     *
     * @return mixed
     * @throws Exception
     */
    public function add($model, $controllerId = null, $urlParamAttrs = ['seo_name'])
    {
        $section = $this->getEntityService($model)->getTableName();
        $location = $this->getLocation($model, $controllerId, $urlParamAttrs);
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $section], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $current = $this->getCurrentSitemap($entity_type, $model);
        if ($current){
            throw new Exception("sitemap with entity_type_id: {$entity_type->id} and entity_id: {$model->id} exists. Please Check");
        }

        $form = new SitemapForm();
        $form->entity_type_id = $entity_type->id;
        /**
         * Если массив значит прилетел роут
         */
        if (is_array($controllerId)) {
            $parts = explode('/',$controllerId[0]);
            $controllerId = $parts[0];
        }

        $form->controller_id = $controllerId;
        $form->entity_id = $model->id;
        if ($model->hasAttribute('domain_id')) {
            $form->domain_id = $model->domain_id;
        }

        $form->location = $location;
        $form->section = $section;

        return $this->create($form);
    }

    /**
     * Обновить карту саита
     *
     * @param ActiveRecord $model
     * @param string $controllerId
     * @param array $urlParamAttrs
     *
     * @return mixed
     * @throws Exception
     */
    public function refresh($model, $controllerId = null, $urlParamAttrs = ['seo_name'])
    {
        $section = $this->getEntityService($model)->getTableName();
        $location = $this->getLocation($model, $controllerId, $urlParamAttrs);
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $section], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $current = $this->getCurrentSitemap($entity_type, $model);
        if (! $current){
            return $this->add($model, $controllerId, $urlParamAttrs);
        }

        if ($current->hasAttribute('is_deleted') && $current->is_deleted == IsDeletedEnum::DELETED){
            $current->undelete();
        }

        if ($current->location == $location){
            return;
        }

        $data = [];
        $data['location'] = $location;

        return $this->updateByModel($current, $data);
    }


    /**
     * удалить карту саита
     *
     * @param ActiveRecord $model
     *
     * @return mixed
     * @throws Exception
     */
    public function remove($model)
    {
        $section = $this->getEntityService($model)->getTableName();
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $section], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $condition = [
            'entity_type_id' => $entity_type->id,
            'entity_id' => $model->id,
        ];

        if ($model->hasAttribute('domain_id')) {
            $condition['origin_domain_id'] = $model->domain_id;
        }

        return $this->deleteAllByCondition($condition);
    }
}