<?php

namespace concepture\yii2handbook\services\datahandlers;

use common\enum\BookmakerRatingEnum;
use common\enum\ReviewStatusEnum;
use common\services\ReviewService;
use common\services\traits\ServicesTrait;
use concepture\yii2logic\dataprocessor\DataHandler;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\services\Service;
use Exception;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class EntitySaveDataHandler
 * @package concepture\yii2handbook\services\datahandlers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntitySaveDataHandler extends DataHandler
{
    use ServicesTrait;

    public $service;
    
    public function setupQuery(ActiveQuery $query, $inputData = null)
    {
        parent::setupQuery($query, $inputData);
        $query->active();
        $query->notDeleted();
    }

    public function processModel(&$data, &$inputData = null)
    {
        parent::processModel($data, $inputData);
        $this->getService()->updateById($data['id'], [], '', false);
    }
    

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }
}