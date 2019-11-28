<?php
namespace concepture\yii2handbook\services;

use concepture\yii2logic\forms\Model;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\services\Service;
use yii\db\ActiveQuery;
use Yii;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServices;

/**
 * Class TagsService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class TagsService extends Service
{
    use HandbookServices;

    protected function beforeCreate(Model $form)
    {
        $form->user_id = Yii::$app->user->identity->id;
    }

    /**
     * Для расширения запроса для вывода каталога и списка для выпадашек
     *
     * @see \concepture\yii2logic\services\traits\CatalogTrait::extendCatalogTraitQuery
     * @param ActiveQuery $query
     */
    protected function extendCatalogTraitQuery(ActiveQuery $query)
    {
        $sql = "domain_id = :domain_id OR domain_id IS NULL";
        $query->andWhere($sql, [':domain_id' => $this->domainService()->getCurrentDomainId()]);
        $sql = "locale = :locale OR locale IS NULL";
        $query->andWhere($sql, [':locale' => $this->localeService()->getCurrentLocaleId()]);
        $query->andWhere(['is_deleted' => IsDeletedEnum::NOT_DELETED]);
    }
}
