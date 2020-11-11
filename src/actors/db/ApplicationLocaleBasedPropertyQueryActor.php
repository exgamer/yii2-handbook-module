<?php
namespace concepture\yii2handbook\actors\db;

use concepture\yii2logic\actors\db\QueryActor;
use Yii;
use concepture\yii2logic\db\HasPropertyActiveQuery;

/**
 * Class LocaleBasedPropertyQueryActor
 * @package concepture\yii2logic\actors\db
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ApplicationLocaleBasedPropertyQueryActor extends QueryActor
{
    public function run()
    {
        if ($this->query instanceof HasPropertyActiveQuery) {
            $this->query->applyPropertyUniqueValue(['domain_id' => Yii::$app->domainService->getCurrentDomainId(), 'locale_id' => 1]);
        }
    }
}