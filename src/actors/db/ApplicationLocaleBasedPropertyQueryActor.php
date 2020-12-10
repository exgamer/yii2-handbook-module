<?php
namespace concepture\yii2handbook\actors\db;

use Yii;
use concepture\yii2logic\actors\db\QueryActor;
use concepture\yii2logic\db\HasPropertyActiveQuery;
use concepture\yii2logic\models\interfaces\IAmDictionaryInterface;

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
            // Для мультиязычных сущностей которые не являются словарями нельзя применять данный код
            // Только для словарей
            if ($model instanceof IAmDictionaryInterface && $model->hasAttribute('locale_id')) {
                $this->query->applyPropertyUniqueValue(['locale_id' => Yii::$app->localeService->getApplicationLocaleId()]);
            }
        }
    }
}