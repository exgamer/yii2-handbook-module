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
        $currenciesForTranslate = $currencies;

        if (!$currencies) {
            throw new \yii\base\Exception('Locales is not found');
        }

        $rows = [];
        foreach ($currencies as $currency) {
            foreach ($currenciesForTranslate as $translateCurrency) {
                if (!Currencies::exists($translateCurrency['code'])) {
                    continue;
                }

                $caption = null;
                $symbol = null;
                try {
                    $caption = Currencies::getName($currency['code'], $translateCurrency['locale']);
                } catch (Exception $e) {
                    dump($e->getMessage());
                }

                $rows[] = [
                    'entity_id' => (int) $currency['id'],
                    'locale' => (int) $translateCurrency['id'],
                    'name' => $caption ? StringHelper::mb_ucfirst($caption) : 'UNKNOWN',
                    'symbol' => $caption ? StringHelper::mb_ucfirst($caption) : 'UNKNOWN',
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
