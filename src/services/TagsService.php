<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\forms\Model;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;
use concepture\yii2handbook\services\traits\ReadSupportTrait;
use concepture\yii2handbook\services\traits\ModifySupportTrait;

/**
 * Class TagsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsService extends Service
{
    use HandbookServices;
    use ReadSupportTrait;
    use ModifySupportTrait;

    protected function beforeCreate(Model $form)
    {
        $form->user_id = Yii::$app->user->identity->id;
        $this->setCurrentDomain($form);
        $this->setCurrentLocale($form);
    }

    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @see \concepture\yii2logic\services\traits\CatalogTrait::extendCatalogTraitQuery
     * @param ActiveQuery $query
     */
    protected function extendCatalogTraitQuery(ActiveQuery $query)
    {
        $this->applyNotDeleted($query);
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
}
