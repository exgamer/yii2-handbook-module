<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\forms\UrlHistoryForm;
use concepture\yii2handbook\models\UrlHistory;
use concepture\yii2handbook\services\interfaces\UrlHistoryInterface;
use concepture\yii2handbook\services\traits\SitemapSupportTrait;
use concepture\yii2handbook\traits\ServicesTrait;
use concepture\yii2logic\helpers\UrlHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class UrlHistoryService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UrlHistoryService extends Service
{
    use ServicesTrait;
    use HandbookModifySupportTrait;
    use HandbookReadSupportTrait;

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


    /**
     * Обновить
     *
     * @param ActiveRecord $model
     * @param null $moduleId
     * @param string $controllerId
     * @param string $actionId
     * @param array $urlParamAttrs
     *
     * @return mixed
     */
    public function refresh($model, $moduleId = null, $controllerId = null, $actionId = "view", $urlParamAttrs = ['seo_name'])
    {
        $tableName = $this->getEntityService($model)->getTableName();
        $location = UrlHelper::getLocation($model, $urlParamAttrs,  $controllerId, $actionId, $moduleId);
        $entity_type = $this->entityTypeService()->getOneByCondition(['table_name' => $tableName], true);
        if(! $entity_type) {
            throw new Exception("Entity type {$section} not found.");
        }

        $current = $this->getOneByCondition([
            'entity_type_id' => $entity_type->id,
            'entity_id' => $model->id,
            'location' => $location,
        ]);

        if ($current){
            return true;
        }

        $last = $this->getOneByCondition(function(ActiveQuery $query) use($entity_type, $model) {
            $query->andWhere([
                'entity_type_id' => $entity_type->id,
                'entity_id' => $model->id,
            ]);
            $query->orderBy('id DESC');
        });

        $form = new UrlHistoryForm();
        $form->entity_type_id = $entity_type->id;
        $form->entity_id = $model->id;
        $form->location = $location;
        if ($last){
            $form->parent_id = $last->id;
        }

        return $this->create($form);
    }

    public function regenerate()
    {
        Yii::info('Url history regenerate start...');
        $entities = $this->entityTypeService()->catalog('id', 'table_name');
        foreach ($entities as $entity){
            $service = $this->getServiceByEntityTable($entity);
            if (! $service){
                continue;
            }

            if (! $service instanceof UrlHistoryInterface){
                Yii::info($entity . ' service not instance of UrlHistoryInterface. no processed');
                continue;
            }
            Yii::info('Url history regenerate for ' . $entity);
            /**
             * @todo тут надо заменить выборку всего на использование  concepture\yii2logic\dataprocessor\DataProcessor
             * иначе на больших данных умрет
             */
            $models = $service->getAllbyCondition(function(ActiveQuery $query) use ($service){
                $model = $service->getRelatedModel();
                $where = [];
                if ($model->hasAttribute('status')){
                    $where['status'] = StatusEnum::ACTIVE;
                }

                if ($model->hasAttribute('is_deleted')){
                    $where['is_deleted'] = IsDeletedEnum::NOT_DELETED;
                }

                if (! empty($where)){
                    $query->andWhere($where);
                }
            });
            if (empty($models)){
                continue;
            }

            foreach ($models as $model){
                $service->updateById($model->id, []);
            }
        }

        Yii::info('Url history regenerate end...');
    }
}