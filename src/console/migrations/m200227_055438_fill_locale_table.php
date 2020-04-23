<?php

use concepture\yii2logic\console\migrations\Migration;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class m200227_055438_fill_locale_table
 */
class m200227_055438_fill_locale_table extends Migration
{
    function getTableName()
    {
        return 'locale';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $usedLocales = Yii::$app->domainService->getDomainMapLocales();
        $connection = $this->getDb();
        if (! $this->isColumnExists("old_locale")) {
            $this->createColumn("old_locale", $this->string(2));
            $connection->createCommand('UPDATE locale SET old_locale=locale')->execute();
        }

        $rows = $connection->createCommand('SELECT * FROM locale')->queryAll();
        $rows = \yii\helpers\ArrayHelper::map($rows, 'locale', 'id');
        $localeData = $this->getLocaleData();
        $currentLocales = [
            'ru' => 'ru',
            'en' => 'en',
            'by' => 'be',
            'kz' => 'kk',
            'ua' => 'uk',
            'us' => 'en',
            'ng' => 'en',
            'za' => 'en',
            'ke' => 'en',
            'au' => 'en',
            'es' => 'es',
            'co' => 'es',
            'mx' => 'es',
            'it' => 'it',
            'pl' => 'pl',
            'fr' => 'fr',
            'se' => 'sv',
            'pt' => 'pt',
            'dk' => 'da',
            'gr' => 'el',
            'ge' => 'ka',
        ];

        if (empty($rows)){
            $currentLocales =[];
        }

        /**
         * запрос для конвертации id удаленных локалей в новую после обьединения
         *
         * UPDATE table SET locale='en' WHERE locale IN('us', 'ng', 'za', 'ke', 'au')
         * UPDATE table SET locale=2 WHERE locale IN(6,7,8,9,10,11)
         * UPDATE table SET locale='es' WHERE locale IN ('co', 'mx')
         * UPDATE table SET locale=12 WHERE locale IN(13,14)
         *
         * UPDATE table SET locale='be' WHERE locale IN ('by')
         *
         * UPDATE table SET locale='sv' WHERE locale IN ('se')
         *
         * UPDATE table SET locale='da' WHERE locale IN ('dk')
         *
         *
         * UPDATE table SET locale='el' WHERE locale IN ('gr')
         *
         * UPDATE table SET locale='ka' WHERE locale IN ('ge')
         *
         */

        $this->execute("SET foreign_key_checks = 0;");
        $this->execute("DELETE FROM locale WHERE locale IN ('us', 'ng', 'uk','za', 'ke', 'au', 'co', 'mx')");
        $this->execute("SET foreign_key_checks = 1;");
        foreach ($rows as $locale => $id){
            if (! isset($currentLocales[$locale])){
                continue;
            }

            if ($locale == $currentLocales[$locale]){
                continue;
            }

            $this->execute("UPDATE locale SET locale='{$currentLocales[$locale]}' WHERE id={$id}");
        }

        $flip = array_flip($currentLocales);
        $flip = array_unique($flip);
        $insertData = [];
        foreach ($localeData as $iso => $caption) {
            if (isset($flip[$iso])){
                continue;
            }

            $insertData[] = [
                'locale' => $iso,
                'caption' => $caption,
                'status' => isset($usedLocales[$iso]) ? StatusEnum::ACTIVE : StatusEnum::INACTIVE,
            ];
        }

        $connection->createCommand('SET sql_mode=""')->execute();
        $result = $this->batchInsert($this->getTableName(), ['locale', 'caption', 'status'], $insertData);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_055438_fill_currency_table cannot be reverted.\n";

        return false;
    }

    /**
     * @return mixed
     */
    private function getLocaleData()
    {
        $result = [];
        // Open the file for reading
        if (($h = fopen(__DIR__ . "/../data/language_codes.csv", "r")) !== FALSE) {
            // Convert each line into the local $data variable
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                // Read the data from a single line
                $result[$data[0]] = $data[1];
            }

            // Close the file
            fclose($h);
        }

        array_shift($result);

        return $result;
    }
}
