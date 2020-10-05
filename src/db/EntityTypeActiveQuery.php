<?php

namespace concepture\yii2handbook\db;

use concepture\yii2logic\helpers\StringHelper;
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
 *       $query->with(['relatedEntity' => function(\concepture\yii2logic\db\ActiveQuery $query){
 *          $query->with('bookmaker');
 *       }]);
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
                if ($name !== 'relatedEntity') {
                    $relation = $model->getRelation($name);
                    $relation->primaryModel = null;
                }else{
                    $relation = new static($this->modelClass);
                }

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

    public function search_file($folderName, $fileName){
        // открываем текущую папку
        $dir = opendir($folderName);
        // перебираем папку
        while (($file = readdir($dir)) !== false){ // перебираем пока есть файлы
            if($file != "." && $file != ".."){ // если это не папка
                if(is_file($folderName."/".$file)){ // если файл проверяем имя
                    // если имя файла нужное, то вернем путь до него
                    if($file == $fileName) return $folderName."/".$file;
                }
                // если папка, то рекурсивно вызываем search_file
                if(is_dir($folderName."/".$file)) return $this->search_file($folderName."/".$file, $fileName);
            }
        }
        // закрываем папку
        closedir($dir);
    }

    public function findWith($with, &$models)
    {
        $relatedKey = 'relatedEntity';
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
        if (isset($relations[$relatedKey])) {
            $rel = $relations[$relatedKey];
            $relWith = $rel->with;
            unset($relations[$relatedKey]);
            $types = [];
            foreach ($models as $model) {
                $eid = is_object($model) ? $model->entity_id : $model['entity_id'];
                $etid = is_object($model) ? $model->entity_type_id : $model['entity_type_id'];
                $types[$etid] [$eid] = $eid;
            }
            $result = [];
            $names = [];
            foreach ($types as $id => $ids) {
                $name = \Yii::$app->entityTypeService->catalogValue($id, 'id', 'table_name');
                $names[$id] = $name;
                $nameSpace = '\common\models\\';
                $parts = explode('_', $name);
                $parts = array_map(function($v){
                    return ucfirst($v);
                }, $parts);
                $className = implode('', $parts);
                $modelClass = $nameSpace . $className;
                /**
                 * @TODO если не нашли класс о пробуем искать в папке common/models
                 * добавить поиск по подпроектам frontend  backend
                 */
                if (! class_exists($modelClass)) {
                    $path = $this->search_file(Yii::getAlias('@common/models'), $className.'.php');
                    if ($path) {
                        $path = str_replace(Yii::getAlias('@common/models') , '/common/models', $path);
                        $path = str_replace('.php' , '', $path);
                        $path = str_replace('/' , '\\', $path);
                        $modelClass = $path;
                    }
                }

                $q = $modelClass::find()->andWhere(['id' => $ids])->asArray($this->asArray)->indexBy('id');
                /**
                 * Поддержка связей для связанных сущностей
                 */
                if ($relWith) {
                    $model = $modelClass::instance();
                    foreach ($relWith as $w) {
                        if ($model->getRelation($w, false)) {
                            $q->with($w);
                        }
                    }
                }

                $result[$id] = $q->all();
            }

            foreach ($models as $key => $model) {
                $eid = is_object($model) ? $model->entity_id : $model['entity_id'];
                $etid = is_object($model) ? $model->entity_type_id : $model['entity_type_id'];
                $name = $names[$etid];
                $value = $result[$etid][$eid] ?? null;
                if ($model instanceof ActiveRecordInterface) {
                    $model->populateRelation($relatedKey, $value);
                } else {
                    $models[$key][$name] = $value;
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
