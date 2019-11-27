<?php
namespace concepture\yii2handbook\models\traits;


use concepture\yii2handbook\models\Tags;
use concepture\yii2logic\helpers\ClassHelper;

/**
 * Trait TagsTrait
 * @package concepture\yii2handbook\models\traits
 */
trait TagsTrait
{
    /**
     * Возвращает ликовочную модель к тегам
     *
     * @return string
     */
    public function getTagsLinkModelClass()
    {
        return ClassHelper::getRelatedClass($this) . "TagsLink";
    }

    /**
     * связь с тегами через линковочную модель
     *
     * !!! ликовочная модель должна иметь метод getEntityLinkField, который вернет названия поля связанной сущности !!!
     *
     * @return mixed
     */
    public function getTags()
    {
        $tagsModelClass = $this->getTagsLinkModelClass();

        return $this->hasMany(
            Tags::className(),
            ['id'=>'tag_id'])
            ->viaTable($tagsModelClass::tableName(),[$tagsModelClass::getEntityLinkField() =>'id'], function ($query) {
//                $query->select(['id', 'caption']);
//                $query->andWhere(['important' => 1])
//                    ->orderBy(['sort' => SORT_DESC]);
            });
    }
}

