<?php

namespace concepture\yii2handbook\services;

use yii\db\ActiveQuery;
use concepture\yii2logic\services\interfaces\UpdateColumnInterface;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2logic\services\traits\UpdateColumnTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\forms\Model;

/**
 * Сервис сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockService extends \concepture\yii2logic\services\Service implements UpdateColumnInterface
{
    use StatusTrait;
    use HandbookReadSupportTrait;
    use HandbookModifySupportTrait;
    use UpdateColumnTrait;

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
     * @inheritDoc
     */
    public function getDataProvider($queryParams = [], $config = [], $searchModel = null, $formName = null, $condition = null)
    {
        if(! $condition) {
            $condition = function (ActiveQuery $query) {
                $query->orderBy(['sort' => SORT_ASC,'id' => SORT_DESC]);
            };
        }

        return parent::getDataProvider($queryParams, $config, $searchModel, $formName, $condition);
    }

    /**
     * Получение всех элементов
     *
     * @return array
     */
    public function getItems()
    {
        // todo: потом сделать что бы урл можно было указывать
        //$url = Url::current();
        $url = '/';
        $result = [];
        $models = $this->getAllByCondition(function(ActiveQuery $query) use ($url) {
            $query->select(['position', 'content']);
            $query->andWhere([
                'url' => $url,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'status' => StatusEnum::ACTIVE
            ]);
            $query->orderBy([
                'position' => SORT_ASC,
                'sort' => SORT_ASC
            ]);
            $query->asArray();
        });
        if(! $models) {
            return $result;
        }

        foreach($models as $data) {
            if(! isset($result[$data['position']])) {
                $result[$data['position']] = null;
            }

            $result[$data['position']] .= $data['content'];
        }

        return $result;
    }
}
