<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\forms\SitemapForm;
use concepture\yii2handbook\services\traits\SitemapGeneratorTrait;
use concepture\yii2handbook\traits\ServicesTrait;
use concepture\yii2logic\enum\IsDeletedEnum;
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
use yii\db\Expression;
use yii\db\Query;
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
    use SitemapGeneratorTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        parent::beforeCreate($form);
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
        $model->last_modified_dt = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
        parent::beforeModelSave($form, $model, $is_new_record);
    }

    /**
     * Добавить в карту саита ссылку
     *
     * @param ActiveRecord $model
     * @param string $controllerId
     * @param array $urlParamAttrs
     *
     * @return mixed
     * @throws \Exception
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
            throw new \Exception("sitemap with entity_type_id: {$entity_type->id} and entity_id: {$model->id} exists. Please Check");
        }

        $form = new SitemapForm();
        $form->entity_type_id = $entity_type->id;
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
     * @throws \Exception
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

        return $this->delete($current);
    }

    /**
     * @param $model
     * @return Service
     */
    protected function getEntityService($model)
    {
        $serviceName = ClassHelper::getServiceName($model);

        return  Yii::$app->{$serviceName};
    }

    /**
     * @param $model
     * @param $controllerId
     * @param $urlParamAttrs
     * @return mixed
     */
    protected function getLocation($model, &$controllerId, $urlParamAttrs)
    {
        $queryParams = [];
        foreach ($urlParamAttrs as $attribute){
            $queryParams[$attribute] = $model->{$attribute};
        }

        $className = ClassHelper::getShortClassName($model);
        if (! $controllerId) {
            $controllerId = Inflector::camel2id($className);
        }

        $urlParams = ArrayHelper::merge([$controllerId . '/view'], $queryParams);
        $frontendUrlManager = UrlHelper::getFrontendUrlManager();

        return $frontendUrlManager->createUrl($urlParams);
    }

    /**
     * Возвращает все варианты секций
     *
     * @return array
     */
    public function getSections()
    {
        $models = $this->getAllByCondition(function (ActiveQuery $query){
            $query->select(['section', 'status', 'is_deleted']);
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED
            ]);
            $query->groupBy('section');
        });

        return ArrayHelper::map($models, 'section', 'section');
    }

    /**
     * Возвращает все по секции
     *
     * @param string $section
     * @return array
     */
    public function getAllBySection($section)
    {
        return $this->getAllByCondition(function (ActiveQuery $query) use ($section){
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'section' => $section,
            ]);
            $query->orderBy("created_at");
        });
    }

    /**
     *  Блок генератора копии с легалбета
     */

    /**
     * Возвращает записи секий файлов  количествлом
     *
     * @return mixed
     */
    public function getRowsSectionCountStat()
    {
        $query = new Query();
        $query->from('sitemap');
        $query->select(['section', 'static_filename', 'static_filename_part', new Expression('COUNT(0) AS `count`')]);
        $query->andWhere([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED
        ]);
        $query->groupBy(['section', 'static_filename', 'static_filename_part']);
        $query->orderBy("section, static_filename_part");


        return $query->all();
    }

    public function getAllBySectionWithoutFilename($section)
    {
        return $this->getAllByCondition(function (ActiveQuery $query) use ($section){
            $query->andWhere('static_filename_id IS NULL');
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'section' => $section,
            ]);
            $query->indexBy('id');
        });
    }
}