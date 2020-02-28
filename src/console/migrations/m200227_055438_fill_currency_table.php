<?php

use yii\db\Migration;

/**
 * Class m200227_055438_fill_currency_table
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200227_055438_fill_currency_table extends Migration
{
    /** @var string */
    private $tableName = 'currency';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable($this->tableName);
        $this->execute("SET foreign_key_checks = 1;");
        $this->renameColumn($this->tableName, 'iso', 'code');
        $this->alterColumn($this->tableName, 'caption', 'varchar(100)');
        $this->renameColumn($this->tableName, 'caption', 'name');
        $this->addColumn($this->tableName, 'symbol', 'varchar(10) NOT NULL');
        $this->addColumn($this->tableName, 'symbol_native', 'varchar(10) NOT NULL');

        $rows = [];
        $data = $this->getCurrencyMap();
        if (!$data) throw new Exception('No data');

        foreach ($data as $code => $item) {
            $rows[$code] = [
                'code' => $code,
                'name' => $item['name'],
                'symbol' => $item['symbol'],
                'symbol_native' => $item['symbol_native'],
            ];
        }

        $connection = $this->getDb();
        $connection->createCommand('SET sql_mode=""')->execute();
        $result = $this->batchInsert($this->tableName, ['code', 'name', 'symbol', 'symbol_native'], $rows);
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
    private function getCurrencyMap()
    {
        return \yii\helpers\Json::decode(
            file_get_contents("http://www.localeplanet.com/api/auto/currencymap.json?name=Y")
        );
    }
}
