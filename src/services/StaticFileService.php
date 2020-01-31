<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\traits\ServicesTrait;
use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Class StaticFileService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticFileService extends Service
{
    use StatusTrait;
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
     * Возвращает robots.txt
     *
     * @return ActiveRecord\
     */
    public function getRobotsFile()
    {
        return $this->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
            'type' => StaticFileTypeEnum::ROBOTS,
        ]);
    }
}