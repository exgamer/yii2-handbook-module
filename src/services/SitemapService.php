<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\forms\SitemapForm;
use concepture\yii2handbook\services\traits\SitemapGeneratorTrait;
use concepture\yii2handbook\services\traits\SitemapModifyTrait;
use concepture\yii2handbook\traits\ServicesTrait;
use concepture\yii2logic\console\traits\OutputTrait;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\UrlHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait as HandbookModifySupportTrait;
use concepture\yii2handbook\services\traits\ReadSupportTrait as HandbookReadSupportTrait;
use concepture\yii2logic\enum\StatusEnum;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class SitemapService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapService extends Service
{
    use StatusTrait;
    use ServicesTrait;
    use HandbookModifySupportTrait;
    use HandbookReadSupportTrait;
    use SitemapGeneratorTrait;
    use SitemapModifyTrait;
    use OutputTrait;

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

    protected function beforeModelSave(Model $form, ActiveRecord $model, $is_new_record)
    {
        $model->last_modified_dt = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
        parent::beforeModelSave($form, $model, $is_new_record);
    }

    /**
     * @param $model
     * @param $controllerId
     * @param $urlParamAttrs
     * @return mixed
     */
    protected function getLocation($model, &$controllerId, $urlParamAttrs)
    {
        $queryParams = [];
        foreach ($urlParamAttrs as $key => $attribute){
            if (filter_var($key, FILTER_VALIDATE_INT) !== false) {
                $queryParams[$attribute] = $model->{$attribute};
                continue;
            }

            $queryParams[$key] = $attribute;
        }

        $className = ClassHelper::getShortClassName($model);
        if (! $controllerId) {
            $controllerId = Inflector::camel2id($className);
        }

        if (! is_array($controllerId)) {
            $controllerId = [$controllerId . '/view'];
        }

        $urlParams = ArrayHelper::merge($controllerId, $queryParams);
        $frontendUrlManager = UrlHelper::getFrontendUrlManager();

        return $frontendUrlManager->createUrl($urlParams);
    }

    /**
     * Возвращает все варианты секций
     *
     * @return array
     */
    public function getSections()
    {
        $models = $this->getAllByCondition(function (ActiveQuery $query){
            $query->select(['section', 'status', 'is_deleted']);
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED
            ]);
            $query->groupBy('section');
        });

        return ArrayHelper::map($models, 'section', 'section');
    }

    /**
     *  Блок генератора копии с легалбета
     */

    /**
     * Возвращает записи секий файлов  количествлом
     *
     * @return mixed
     */
    public function getRowsSectionCountStat()
    {
        $sql = "SELECT COUNT(0) AS `count`, section, static_filename, static_filename_part
                from sitemap
                WHERE status = :STATUS AND is_deleted = :IS_DELETED
                AND domain_id = :DOMAIN_ID OR domain_id IS NULL
                GROUP BY section, static_filename, static_filename_part
                ORDER BY section, static_filename_part
        ";
        $params = [
            ':STATUS' => StatusEnum::ACTIVE,
            ':IS_DELETED' => IsDeletedEnum::NOT_DELETED,
            ':DOMAIN_ID' => Yii::$app->domainService->getCurrentDomainId()
        ];


        return $this->queryAll($sql , $params);
    }
}