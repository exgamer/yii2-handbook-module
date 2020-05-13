<?php

namespace concepture\yii2handbook\db;

use Yii;
use concepture\yii2logic\db\ActiveQuery as Base;
use yii\db\ActiveRecordInterface;

/**
 * AQ для поддержки связей через entity_id и entity_type_id
 * запишет в ключ relatedEntity связанную сущность
 *
 * В модели
 *     public static function find()
 *       {
 *           return Yii::createObject(EntityTypeActiveQuery::class, [get_called_class()]);
 *       }
 * В запросе
 *        $query->with(['relatedEntity']);
 * 
 * Class EntityTypeActiveQuery
 * @package concepture\yii2handbook\db
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityTypeActiveQuery extends Base
{
    private function normalizeRelations($model, $with)
    {
        $relations = [];
        foreach ($with as $name => $callback) {
            if (is_int($name)) {
                $name = $callback;
                $callback = null;
            }
            if (($pos = strpos($name, '.')) !== false) {
                // with sub-relations
                $childName = substr($name, $pos + 1);
                $name = substr($name, 0, $pos);
            } else {
                $childName = null;
            }

            if (!isset($relations[$name])) {
                /**
                 * исключаем relatedEntity
                 */
                $relation = null;
                if ($name !== 'relatedEntity') {
                    $relation = $model->getRelation($name);
                }
                $relation->primaryModel = null;
                $relations[$name] = $relation;
            } else {
                $relation = $relations[$name];
            }

            if (isset($childName)) {
                $relation->with[$childName] = $callback;
            } elseif ($callback !== null) {
                call_user_func($callback, $relation);
            }
        }

        return $relations;
    }

    public function findWith($with, &$models)
    {
        $primaryModel = reset($models);
        if (!$primaryModel instanceof ActiveRecordInterface) {
            /* @var $modelClass ActiveRecordInterface */
            $modelClass = $this->modelClass;
            $primaryModel = $modelClass::instance();
        }
        $relations = $this->normalizeRelations($primaryModel, $with);

        /**
         * BEGIN
         * Для возможности получать связанные сущности по entity_id и entity_type_id
         */
        if (isset($relations['relatedEntity'])) {
            unset($relations['relatedEntity']);
            $types = [];
            foreach ($models as $model) {
                $eid = is_object($model) ? $model->entity_id : $model['entity_id'];
                $etid = is_object($model) ? $model->entity_type_id : $model['entity_type_id'];
                $types[$etid] [$eid] = $eid;
            }
            $result = [];
            foreach ($types as $id => $ids) {
                $name = \Yii::$app->entityTypeService->catalogValue($id);
                /**
                 * @TODO Настройку если модель лежит не тут (пока устраивает)
                 */
                $nameSpace = '\common\models\\';
                $modelClass = $nameSpace . ucfirst($name);
                $result[$id] = $modelClass::find()->andWhere(['id' => $ids])->asArray($this->asArray)->indexBy('id')->all();
            }

            foreach ($models as $model) {
                $eid = is_object($model) ? $model->entity_id : $model['entity_id'];
                $etid = is_object($model) ? $model->entity_type_id : $model['entity_type_id'];
                $value = $result[$etid][$eid] ?? null;
                if ($model instanceof ActiveRecordInterface) {
                    $model->populateRelation('relatedEntity', $value);
                } else {
                    $model[$name] = $value;
                }

            }
        }
        /**
         * END
         */

        /* @var $relation \yii\db\ActiveQuery */
        foreach ($relations as $name => $relation) {
            if ($relation->asArray === null) {
                // inherit asArray from primary query
                $relation->asArray($this->asArray);
            }
            $relation->populateRelation($name, $models);
        }
    }
}