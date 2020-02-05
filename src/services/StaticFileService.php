<?php

namespace concepture\yii2handbook\services;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\models\StaticFile;
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
use yii\web\NotFoundHttpException;

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

    protected function beforeModelSave(Model $form, ActiveRecord $model, $is_new_record)
    {
        if (! $model->last_modified_dt) {
            $model->last_modified_dt = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
        }
        parent::beforeModelSave($form, $model, $is_new_record);
    }

    /**
     * Возвращает все записи по частичному совпадению нахвания секции карты саита
     * @param $filename
     * @return array
     */
    public function getAllBySitemapSection($sectionNamePart)
    {
        return $this->getAllByCondition(function (ActiveQuery $query) use ($sectionNamePart) {
            $query->andWhere(            [
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'extension' => FileExtensionEnum::XML,
            ]);
            $query->andFilterWhere(['like', "filename", $sectionNamePart]);
        });
    }

    public function getFile($filename)
    {
        $parts = explode(".", $filename);
        $extension = array_pop($parts);
        $filename = implode(".", $parts);

        return $this->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
            'filename' => $filename,
            'extension' => $extension,
        ]);
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

    /**
     * Возвращает карту саита
     *
     * @param $filename
     * @return string
     */
    public function getSitemapFile($filename)
    {
        $parts = explode(".", $filename);
        $extension = array_pop($parts);
        $filename = implode(".", $parts);
        if ($extension != FileExtensionEnum::XML){
            return null;
        }

        return $this->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
            'type' => [StaticFileTypeEnum::SITEMAP_INDEX, StaticFileTypeEnum::SITEMAP],
            'filename' => $filename,
            'extension' => $extension,
        ]);
    }

    /**
     * Возвращает список файлов sitemap для индексного фаила
     *
     * @return mixed
     */
    public function getSitemapIndexList()
    {
        return $this->getAllByCondition(function (ActiveQuery $query){
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'type' => StaticFileTypeEnum::SITEMAP,
            ]);
        });
    }


    /**
     * Зачистить все саитмапы
     *
     * @return mixed
     */
    public function clearSiteMaps()
    {
        return StaticFile::deleteAll([
            'type' => [StaticFileTypeEnum::SITEMAP, StaticFileTypeEnum::SITEMAP_INDEX]
        ]);
    }
}