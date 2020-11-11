<?php
namespace concepture\yii2handbook\actors\db;

use concepture\yii2logic\actors\db\QueryActor;
use Yii;
use concepture\yii2logic\db\HasPropertyActiveQuery;

/**
 * Класс для глобальной модификации запросов к мультиязычным сущностям с property
 * дл выдачи результата на языке приложения
 *
 * Class LocaleBasedPropertyQueryActor
 * @package concepture\yii2logic\actors\db
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ApplicationLocaleBasedPropertyQueryActor extends QueryActor
{
    public function run()
    {
        if ($this->query instanceof HasPropertyActiveQuery) {
            $model = Yii::createObject($this->query->modelClass);
            if ($model->hasAttribute('locale_id')) {
                $this->query->applyPropertyUniqueValue(['locale_id' => Yii::$app->localeService->getApplicationLocaleId()]);
            }
        }
    }
}