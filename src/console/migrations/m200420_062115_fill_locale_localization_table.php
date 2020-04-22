<?php

use Symfony\Component\Intl\Languages;
use concepture\yii2logic\helpers\StringHelper;
use concepture\yii2logic\console\migrations\Migration;

/**
 * Class m200420_062115_fill_locale_localization_table
 *
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class m200420_062115_fill_locale_localization_table extends Migration
{
    /**
     * @return string
     */
    function getTableName()
    {
        return 'locale_localization';
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $connection = $this->getDb();
        $locales = $connection->createCommand('SELECT id, locale FROM locale ORDER BY id')->queryAll();
        $localesForTranslate = $locales;

        if (!$locales) {
            throw new \yii\base\Exception('Locales is not found');
        }

        $rows = [];
        foreach ($locales as $locale) {
            foreach ($localesForTranslate as $translateLocale) {
                if (!Languages::exists($translateLocale['locale'])) {
                    continue;
                }

                $caption = null;
                try {
                    $caption = Languages::getName($locale['locale'], $translateLocale['locale']);
                } catch (Exception $e) {
                    // echo $e->getMessage();
                }

                $rows[] = [
                    'entity_id' => (int) $locale['id'],
                    'locale_id' => (int) $translateLocale['id'],
                    'caption' => $caption ? StringHelper::mb_ucfirst($caption) : 'UNKNOWN',
                ];
            }
        }

        $connection->createCommand('SET sql_mode=""')->execute();
        $result = $this->batchInsert($this->getTableName(), ['entity_id', 'locale_id', 'caption'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
