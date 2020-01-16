<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\models\Robots;
use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Сервис для работы с индексными файлами - robots.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RobotsService extends Service
{
    use StatusTrait;
    use HandbookModifySupportTrait;
    use HandbookReadSupportTrait;
//    use EntityTypeSupportTrait;

    /**
     * @return DomainService
     */
    protected function getDomainService()
    {
        return Yii::$app->domainService;
    }

    /**
     * @inheritDoc
     */
    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        $this->inactivateAll($form->status);
    }

    /**
     * @inheritDoc
     */
    protected function beforeUpdate(Model $form, ActiveRecord $model)
    {
        $this->inactivateAll($form->status);
    }

    /**
     * @inheritDoc
     */
    protected function beforeStatusChange(ActiveRecord $model, $status)
    {
        $this->inactivateAll($status);
    }

    /**
     * Деактивация всех активных записей в рамках домена, если он установлен
     *
     * @param $status
     */
    private function inactivateAll($status)
    {
        if($status != StatusEnum::ACTIVE) {
            return;
        }

        $class = $this->getRelatedModelClass();
        $class::updateAll(
            [
                'status' => StatusEnum::INACTIVE
            ],
            [
                'AND',
                ['status' => StatusEnum::ACTIVE],
                [
                    'OR',
                    [
                        'domain_id' => $this->getDomainService()->getCurrentDomainId()
                    ],
                    [
                        'domain_id' => null
                    ]
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function extendQuery(ActiveQuery $query)
    {
        $this->applyDomain($query);
    }
}