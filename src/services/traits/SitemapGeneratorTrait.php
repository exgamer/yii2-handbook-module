<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Trait SitemapGeneratorTrait
 * @package concepture\yii2handbook\services\traits
 */
trait SitemapGeneratorTrait
{
    /**
     * Возвращает карту саита
     *
     * @return string
     */
    public function getSitemapFile()
    {
        $data = [
            'name' => 'urlset', // "name" required, all else optional
            'attributes' => [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            ],
            [
                'name' => 'url',
                [
                    'name' => 'loc',
                    'value' => 'https://legalbet.ru/best-posts/zhelto-sinij-trend-v-otbore-evro-2016/',
                ],
                [
                    'name' => 'priority',
                    'value' => '0.55',
                ],
            ],
        ];
        $doc = new \DOMDocument("1.0", "UTF-8");
        $xslt = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
        $doc->appendChild($xslt);
        $child = $this->generate_xml_element( $doc, $data );
        if ( $child )
            $doc->appendChild( $child );
        $doc->formatOutput = true; // Add whitespace to make easier to read XML
        $xml = $doc->saveXML();

        $form = new StaticFileForm();
        $form->type = StaticFileTypeEnum::SITEMAP;
        $form->content = $xml;
        $form->extension = FileExtensionEnum::XML;
        $form->filename = "post_part_1";
        $form->is_hidden = 1;
        Yii::$app->staticFileService->create($form);

        d($xml);
        $sections = $this->getSections();
        dump($sections);
        foreach ($sections as $section){
            $models = $this->getAllBySection($section);
            dump($models);
        }

        return "";
    }

    /**
     * Возвращает приоритет элемента для карты саита
     *
     * @param $model
     * @return string
     * @throws Exception
     */
    protected function getPriority($model)
    {
        /*
           Страницы,	изменившиеся	сегодня	–	приоритет	1.0
           Страницы,	изменившиеся	вчера	–	приоритет	0.9
           Страницы,	изменившиеся	на	этои 	неделе	–	приоритет	0.8
           Страницы	новостеи ,	прогнозов	и	др.	(которые	в	RSS)	–	приоритет	не	менее	0.6
           Все	остальные	страницы	приоритет	-	приоритет	0.5
         */
        $last_modified_dt = $model->last_modified_dt;
        $current_dt = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
        $section = $model->section;
        $dt = new \DateTime($current_dt);
        if($last_modified_dt > $dt->modify('-20 hours')->format('Y-m-d H:i:s')){

            return '1';
        }elseif($last_modified_dt > $dt->modify('-40 hours')->format('Y-m-d H:i:s')){

            return '0.9';
        }elseif($last_modified_dt > $dt->modify('-7 days')->format('Y-m-d H:i:s')){

            return '0.8';
        }

        /**
         * @todo тут надо универсальное решение
         */
//        elseif(in_array($section, array('news', 'bonus', 'forecast'))){
//
//            return '0.6';
//        }

        return '0.55';
    }
}
