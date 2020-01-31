<?php
namespace concepture\yii2handbook\services;

use concepture\yii2handbook\enum\SettingsTypeEnum;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;
use Exception;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\services\traits\ReadSupportTrait as CoreReadSupportTrait;

/**
 * Class SettingsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SettingsService extends Service
{
    use HandbookServices;
    use ReadSupportTrait;
    use ModifySupportTrait;
    use CoreReadSupportTrait;

    protected function beforeCreate(Model $form)
    {
        $this->setCurrentDomain($form);
        $this->setCurrentLocale($form);
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
        $this->applyLocale($query);
    }

    /**
     * Переопределено для возможности автоматического добавления настроек, не найденных в БД при вызове catalogValue
     *
     * @todo пока рассчитано не небольшое количество настроек, т.к. считывается весь кататлог в статику parent::catalogValue
     * @todo : пахнет от этого метода
     *
     * @see \concepture\yii2logic\services\traits\CatalogTrait::catalogValue($key)
     * Возвращает значение из каталога по ключу
     * Для использования у search модели должны быть определены методы
     * getListSearchAttribute и getListSearchKeyAttribute
     *
     * @param $key
     * @return mixed|null
     * @throws Exception
     */
    public function catalogValue($key, $value = null, $type = SettingsTypeEnum::TEXT)
    {
        $catalogValue = parent::catalogValue($key);
        if ($catalogValue){
            return $catalogValue;
        }

        if (! $value){
            return $catalogValue;
        }

        $formClass = $this->getRelatedFormClass();
        $form = new $formClass();
        $form->name = $key;
        $form->value = $value;
        $form->type = $type;
        $this->create($form);

        return $value;
    }
}
