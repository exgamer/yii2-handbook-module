<?php

use Symfony\Component\Intl\Countries;
use concepture\yii2logic\helpers\StringHelper;
use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200422_083242_fill_country_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200422_083242_fill_country_localization_table extends Migration
{
    /**
     * @return string
     */
    function getTableName()
    {
        return 'country_localization';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = $this->getDb();
        $countries = $connection->createCommand('SELECT id, iso FROM country ORDER BY iso')->queryAll();
        $locales = $connection->createCommand('SELECT id, locale FROM locale ORDER BY id')->queryAll();
//        d($countries[0], $locales[0]);

        if (!$countries || !$locales) {
            throw new \yii\base\Exception('Locales or countries is not found');
        }

        $rows = [];
        foreach ($countries as $country) {
            foreach ($locales as $locale) {
                if (!Countries::exists(mb_strtoupper($country['iso']))) {
                    continue;
                }

                $caption = null;
                try {
                    $caption = Countries::getName(mb_strtoupper($country['iso']), $locale['locale']);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                $rows[] = [
                    'entity_id' => (int) $country['id'],
                    'locale' => (int) $locale['id'],
                    'caption' => $caption ? StringHelper::mb_ucfirst($caption) : 'UNKNOWN',
                ];
            }
        }

        $connection->createCommand('SET sql_mode=""')->execute();
        $result = $this->batchInsert($this->getTableName(), ['entity_id', 'locale', 'caption'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}