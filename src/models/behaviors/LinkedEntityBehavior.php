<?php
namespace concepture\yii2handbook\models\behaviors;

use common\pojo\LinkedEntity;
use concepture\yii2logic\db\ActiveQuery;
use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Exception;


class LinkedEntityBehavior extends Behavior
{
    /**
     * Массив атрибутов которые сохраняются json
     *
     * @var array
     */
    public $linkAttr = [];
    public $pojoClass = LinkedEntity::class;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT=>'saveLinks',
            ActiveRecord::EVENT_AFTER_UPDATE=>'saveLinks',
        ];
    }

    public function saveLinks()
    {
        if(empty($this->linkAttr) ) {
            return null;
        }

        foreach ($this->linkAttr as $attr => $config) {
            if (! $this->owner->{$attr}) {
                continue;
            }

            $entity_id = $this->owner->id;
            $class = $this->getAttributeConfigData($config, 'class');
            $tableName = trim($class::tableName(), '{}%');
            $entity_type_id =  Yii::$app->entityTypeService->catalogKey($tableName, 'id', 'table_name');
            $linkClass = $this->getAttributeConfigData($config, 'link_class');
            $linkService = $linkClass::getService();
            $linkAttribute = $this->getAttributeConfigData($config, 'link_attribute');
            $linkClass::deleteAll(['entity_type_id' => $entity_type_id, 'entity_id' => $entity_id]);
            $fields = [
                'entity_type_id',
                'entity_id',
                $linkAttribute,
                'status',
                'sort',
            ];
            $insertData = [];
            foreach ($this->owner->{$attr} as $key => $data) {
                $insertData[] = [
                    $entity_type_id,
                    $entity_id,
                    $data['link_id'],
                    isset($data['status']) ? $data['status'] : 0,
                    $key+1
                ];
            }

            $linkService->batchInsert($fields, $insertData);
        }
    }

    public function getLinkAttributes()
    {
        $behavior = ClassHelper::getBehavior($this->owner, static::class);
        $attrs = $behavior['linkAttr'] ?? [];
        $linkAttrs = [];
        foreach ($attrs as $key => $value){
            if ( filter_var($key, FILTER_VALIDATE_INT) === false ) {
                $linkAttrs[$key] = $value;
            }
        }

        return $linkAttrs;
    }

    public function getLinkModels($attribute)
    {
        $linkAttrs = $this->getLinkAttributes();
        if (! isset($linkAttrs[$attribute])){
            throw new Exception($attribute . " is no linked data");
        }
        
        if ($this->owner->{$attribute}){
            return $this->owner->{$attribute};
        }

        $class = $this->getAttributeConfigData($config, 'class');
        $service = $class::getService();
        $linkClass = $this->getAttributeConfigData($linkAttrs[$attribute], 'link_class');
        $linkService = $linkClass::getService();
        $allLinked = $service->getAllModelsForList();
        if (!$allLinked) {
            $model = Yii::createObject($this->pojoClass);

            return [$model];
        }

        $entity_id = $this->owner->id;
        $tableName = trim($class::tableName(), '{}%');
        $entity_type_id =  Yii::$app->entityTypeService->catalogKey($tableName, 'id', 'table_name');
        $linkAttribute = $this->getAttributeConfigData($config, 'link_attribute');
        $linkModels = $linkService->getAllByCondition(function(ActiveQuery $query) use($entity_type_id, $entity_id, $linkAttribute) {
            $query->andWhere([
                'entity_type_id' => $entity_type_id,
                'entity_id' => $entity_id,
            ]);

            $query->indexBy([$linkAttribute]);
        });

        $data = [];
        foreach ($allLinked as $model) {
            $link = isset($linkModels[$model->id]) ? $linkModels[$model->id] : null;
            $sort = $link ? $link->sort : 1;
            $model = new $this->pojoClass();
            $model->{$linkAttribute} = $model->id;
            $model->name = $model->toString();
            $model->status = $link ? $link->status : 0;
            $model->sort = $sort;

            $data[] = $model;
        }

        usort($data, function ($a, $b) {
            return $a->sort > $b->sort;
        });

        return $data;
    }

    public function getAttributeConfigData($attributeData, $key)
    {
        if (! is_array($attributeData)){
            return $attributeData;
        }

        return $attributeData[$key] ?? null;
    }
}