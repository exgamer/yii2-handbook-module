<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\forms\SitemapForm;
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

        $current = $this->getOneByCondition([
            'entity_type_id' => $entity_type->id,
            'entity_id' => $model->id,
        ]);

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

        $current = $this->getOneByCondition([
            'entity_type_id' => $entity_type->id,
            'entity_id' => $model->id,
        ]);

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

        return $this->updateById($current->id, $data);
    }

    /**
     * удалить карту саита
     *
     * @param ActiveRecord $model
     *
     * @return mixed
     */
    public function remove($model)
    {
        $section = $this->getEntityService($model)->getTableName();
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $section], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $current = $this->getOneByCondition([
            'entity_type_id' => $entity_type->id,
            'entity_id' => $model->id,
        ]);
        if (! $current){
            return true;
        }

        return $this->delete($current);
    }
}