<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\models\EntityType;
use common\enum\EntityTypePositionEnum;

/**
 * Сервис для работы с
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class EntityTypePositionService extends Service
{
    use HandbookServices;
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;
    use StatusTrait;

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);

        parent::beforeCreate($form);
    }

    /**
     * Метод для расширения find()
     * !! ВНимание эти данные будут поставлены в find по умолчанию все всех случаях
     *
     * @param ActiveQuery $query
     * @see \concepture\yii2logic\services\Service::extendFindCondition()
     */
    protected function extendQuery(ActiveQuery $query)
    {
        $this->applyDomain($query);
    }

    /**
     * @inheritDoc
     *
     * @param bool $cache
     */
    public function getOneByCondition($condition = null, $cache = false, $asArray = false)
    {
        if( ! Yii::$app->has('cache') || ! $cache) {
            return parent::getOneByCondition($condition, $asArray);
        }

        return $this->getDb()->cache(function () use($condition, $asArray) {
            return parent::getOneByCondition($condition, $asArray);
        });
    }

    /**
     * Создание позиций по типу сущности
     *
     * @param EntityType $entity
     * @param array $domains
     * @param string $alias
     * @param string $caption
     * @param int $max_count
     * @return array|bool
     * @throws \ReflectionException
     */
    public function createFromEntityType(EntityType $entity, array $domains, string $alias, string $caption, $max_count = 10)
    {
        $errors = [];
        foreach ($domains as $domain) {
            $this->domainService()->setVirtualDomainId($domain->id);
            $position = $this->getOneByCondition([
                'alias' => $alias,
                'status' => StatusEnum::ACTIVE,
            ]);
            if (! empty($position)) {
                continue;
            }

            $form = $this->getRelatedForm();
            $form->caption = $caption;
            $form->alias = $alias;
            $form->domain_id = $domain->id;
            $form->entity_type_id = $entity->id;
            $form->max_count = $max_count;
            $form->status = StatusEnum::ACTIVE;
            $result = $this->create($form);
            if ($result === false){
                $errors[$domain->id] =  $form->getErrors();
            }
        }

        return $errors;
    }
}