<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2article\models\PostTagsLink;
use concepture\yii2handbook\enum\TagTypeEnum;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use Yii;
use yii\db\ActiveQuery;

/**
 * Trait TagsTrait
 * @package concepture\yii2handbook\services\traits
 */
trait TagsTrait
{
    use HandbookServices;

    /**
     * Создаем связки сущности и тегов
     *
     * @param integer $entityId
     * @param array $selectedTags
     * @param string $customTags
     */
    public function addTags($entityId, $selectedTags, $customTags = null)
    {
        $customIds = $this->resolveCustomTags($customTags);
        $tagIds = array_merge($selectedTags, $customIds);
        $tagIds = array_unique($tagIds);
        $modelClass = $this->getRelatedModelClass();
        $currentLinks = $this->getAllByCondition(function(ActiveQuery $query) use ($modelClass, $entityId){
            $query->andWhere([ $modelClass::getEntityLinkField()=> $entityId]);
            $query->indexBy('tag_id');
        });
        $currentTagsIds = array_keys($currentLinks);
        $deletedTagsIds = array_diff($currentTagsIds, $tagIds);
        $modelClass::deleteAll(['tag_id' => $deletedTagsIds, $modelClass::getEntityLinkField() => $entityId]);
        if (! empty($tagIds)){
            $insertData = [];
            foreach ($tagIds as $id){
                $insertData[] = [
                    $entityId,
                    $id
                ];
            }
            $this->batchInsert([$modelClass::getEntityLinkField(), 'tag_id'], $insertData);
        }
    }

    /**
     * резольвим кастомную строку с тегами и создаем если их нет
     *
     * @param string $customTags
     * @return array
     */
    protected function resolveCustomTags($customTags)
    {
        if (! $customTags){
            return [];
        }

        $tagsArray = explode(",", $customTags);
        $trimmedTagsArray = [];
        foreach ($tagsArray as $tag){
            $trimmedTagsArray[] = trim($tag);
        }
        $tagsArray = $trimmedTagsArray;
        $domainId = Yii::$app->domainService->getCurrentDomainId();
        /**
         * Вычисляем сществующие записи из за того что индекс уникальности не работает если одно из полей NULL
         * в случае с тегами domain_id может быть null
         */
        $existTags = $this->tagsService()->getAllByCondition(function(ActiveQuery $query) use ($tagsArray, $domainId){
            $query->andWhere(['caption' => $tagsArray]);
            if ($domainId){
                $query->andWhere("domain_id = :domain_id", [':domain_id' => $domainId]);
            }else{
                $query->andWhere("domain_id IS NULL");
            }
            $query->indexBy('caption');
        });
        foreach ($tagsArray as $tag){
            if (isset($existTags[$tag])){
                continue;
            }
            $newTags[] = [
                Yii::$app->user->identity->id,
                $domainId,
                TagTypeEnum::CUSTOM,
                $tag,
            ];
        }

        if (! empty($newTags)){
            $this->tagsService()->batchInsert(['user_id', 'domain_id', 'type', 'caption'], $newTags);
        }

        $existTags = $this->tagsService()->getAllByCondition(function(ActiveQuery $query) use ($tagsArray, $domainId){
            $query->andWhere(['caption' => $tagsArray]);
            if ($domainId){
                $query->andWhere("domain_id = :domain_id", [':domain_id' => $domainId]);
            }else{
                $query->andWhere("domain_id IS NULL");
            }

            $query->indexBy('id');
        });

        return array_keys($existTags);
    }
}

