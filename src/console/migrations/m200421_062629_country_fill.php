<?php

use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200421_062629_country_fill
 */
class m200421_062629_country_fill extends Migration
{
    function getTableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = $this->getDb();
        $this->createColumn('iso2', $this->string(3));
        $this->createColumn('code', $this->smallInteger());
        $this->createColumn('iso_3166_2', $this->string(100));
        $this->createColumn('region', $this->string(100));
        $this->createColumn('sub_region', $this->string(100));
        $this->createColumn('region_code', $this->smallInteger());
        $this->createColumn('sub_region_code', $this->smallInteger());
        $this->createColumn('intermediate_region', $this->string(100));
        $this->createColumn('intermediate_region_code', $this->smallInteger());
        $rows = $connection->createCommand('SELECT * FROM country')->queryAll();
        $rows = \yii\helpers\ArrayHelper::map($rows, 'iso', 'id');
        $data = $this->getData();
        $insertData = [];
        foreach ($data as $countryData) {
            if (isset($rows[strtolower($countryData['alpha-2'])])){
                continue;
            }

            $insertData[] = [
                'iso' => strtolower($countryData['alpha-2']),
                'iso2' => strtolower($countryData['alpha-3']),
                'caption' => $countryData['name'],
                'code' => $countryData['country-code'],
                'iso_3166_2' => $countryData['iso_3166-2'],
                'region' => $countryData['region'],
                'sub_region' => $countryData['sub-region'],
                'region_code' => $countryData['region-code'],
                'sub_region_code' => $countryData['sub-region-code'],
                'intermediate_region' => $countryData['intermediate-region'],
                'intermediate_region_code' => $countryData['intermediate-region-code'],
                'status' => \concepture\yii2logic\enum\StatusEnum::ACTIVE,
                'locale' => 1,
            ];
        }

        $connection->createCommand('SET sql_mode=""')->execute();
        $this->batchInsert($this->getTableName(), [
            'iso',
            'iso2',
            'caption',
            'code',
            'iso_3166_2',
            'region',
            'sub_region',
            'region_code',
            'sub_region_code',
            'intermediate_region',
            'intermediate_region_code',
            'status',
            'locale',
        ], $insertData);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200421_062629_country_fill cannot be reverted.\n";

        return false;
    }

    /**
     * @return array
     */
    private function getData()
    {
        $str = file_get_contents(__DIR__ . "/../data/countries.json");

        return json_decode($str, true);
    }
}
