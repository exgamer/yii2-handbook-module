<?php

namespace concepture\yii2handbook\services;

use Yii;
use yii\db\ActiveRecord;
use concepture\yii2logic\services\Service;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class EntityTypeService
 * @package concepture\yii2handbook\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class EntityTypeService extends Service
{
    /**
     * @see \concepture\yii2logic\services\traits\CatalogTrait
     */
    protected function catalogKeyPreAction(&$value, &$catalog)
    {
        $value = trim($value, '{}');
    }

    /**
     * Получение одной записи по условию
     *
     * @param array| \Closure $condition
     * @param boolean $cache
     *
     * @return ActiveRecord|null
     */
    public function getOneByCondition($condition = null, $cache = false, $asSql = false, $fetchMode = null)
    {
        if( ! Yii::$app->has('cache') || ! $cache) {
            return parent::getOneByCondition($condition, $asSql, $fetchMode);
        }

        return $this->getDb()->cache(function () use($condition, $asSql, $fetchMode) {
            return parent::getOneByCondition($condition, $asSql, $fetchMode);
        });
    }

    /**
     * Получение записи по названию таблицы
     *
     * @param string $tableName
     * @param boolean $createNotExist - создать  запись если не существует
     * @param boolean $cache
     *
     * @return ActiveRecord|null
     */
    public function getByTableName($tableName, $createNotExist = false, $cache = false)
    {
        $item = $this->getOneByCondition(['table_name' => $tableName], $cache);
        if(! $createNotExist) {
            return $item;
        }

        if (! $item) {
            $form = $this->getRelatedForm();
            $form->table_name = $tableName;
            $form->caption = $tableName;
            $form->status = StatusEnum::ACTIVE;
            $form->sort_module = 1;
            $item = $this->create($form);
        }

        return $item;
    }

    /**
     * Инициализация сущностей из массива названий таблиц
     *
     * @param array $tables
     *
     * @return array
     */
    public function createFromArray(array $tables)
    {
        $result = [];
        foreach ($tables as $table) {
            $result[$table] = $this->getByTableName($table, true);
        }

        return $result;
    }
}