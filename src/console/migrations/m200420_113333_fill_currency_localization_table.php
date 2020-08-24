<?php

use Symfony\Component\Intl\Currencies;
use concepture\yii2logic\helpers\StringHelper;
use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200420_113333_fill_currency_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200420_113333_fill_currency_localization_table extends Migration
{
    /**
     * @return string
     */
    function getTableName()
    {
        return 'currency_localization';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = $this->getDb();
        $currencies = $connection->createCommand('SELECT id, code FROM currency ORDER BY code')->queryAll();
        $locales = $connection->createCommand('SELECT id, locale FROM locale ORDER BY id')->queryAll();

        if (!$currencies || !$locales) {
            return;
        }

        $rows = [];
        foreach ($currencies as $currency) {
            foreach ($locales as $locale) {
                if (!Currencies::exists($currency['code'])) {
                    continue;
                }

                $caption = null;
                $symbol = null;
                try {
                    $caption = Currencies::getName($currency['code'], $locale['locale']);
                    $symbol = Currencies::getSymbol($currency['code'], $locale['locale']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $rows[] = [
                    'entity_id' => (int) $currency['id'],
                    'locale' => (int) $locale['id'],
                    'name' => $caption ? StringHelper::mb_ucfirst($caption) : 'UNKNOWN',
                    'symbol' => $symbol ? StringHelper::mb_ucfirst($symbol) : 'UNKNOWN',
                ];
            }
        }

        $connection->createCommand('SET sql_mode=""')->execute();
        $result = $this->batchInsert($this->getTableName(), ['entity_id', 'locale', 'name', 'symbol'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
