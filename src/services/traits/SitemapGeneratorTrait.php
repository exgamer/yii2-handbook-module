<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\SitemapGeneratorEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use concepture\yii2handbook\models\Sitemap;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\XmlHelper;
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
     * Новый xml документ
     * @param string $rootName
     * @return string
     */
    public function getNewDocument($rootName = 'urlset')
    {
        $data = [
            'name' => $rootName, // "name" required, all else optional
            'attributes' => [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
//                'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            ],

//            [
//                'name' => 'url',
//                [
//                    'name' => 'loc',
//                    'value' => 'https://legalbet.ru/best-posts/zhelto-sinij-trend-v-otbore-evro-2016/',
//                ],
//                [
//                    'name' => 'priority',
//                    'value' => '0.55',
//                ],
//            ],
        ];

        $doc = new \DOMDocument("1.0", "UTF-8");
        $xslt = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
        $doc->appendChild($xslt);
        $child = XmlHelper::generateXmlElement( $doc, $data );
        if ( $child )
            $doc->appendChild( $child );
        $doc->formatOutput = true; // Add whitespace to make easier to read XML

        return $doc->saveXML();
    }

    /**
     * Возвращает static_file по filename
     *
     * @param $filename
     * @return mixed
     */
    public function getStaticFileByFilename($filename)
    {
        $model = Yii::$app->staticFileService->getOneByCondition([
            'filename' => $filename,
            'type' => StaticFileTypeEnum::SITEMAP,
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED
        ]);
        if (! $model){
            $form = new StaticFileForm();
            $form->type = StaticFileTypeEnum::SITEMAP;
            $form->content = $this->getNewDocument();
            $form->extension = FileExtensionEnum::XML;
            $form->filename = $filename;
            $form->is_hidden = 1;
            $model = Yii::$app->staticFileService->create($form);
        }

        return $model;
    }

    /**
     * Возвращает карту саита
     *
     * @return string
     */
    public function getSitemapFile()
    {
        $urlsPerFile = 2;
        $sections = $this->getSections();
        foreach ($sections as $section){
            $models = $this->getAllBySection($section);
            if (empty($models)){
                continue;
            }

            $modelsMap = [];
            $i = 1;
            foreach ($models as $model){
                $modelsMap[$i][strtotime($model->created_at)] = $model;
                if ( count($modelsMap[$i]) >= $urlsPerFile ){
                    $i++;
                }
            }

            d($modelsMap);



            $last_modified_dt = $models[0]->last_modified_dt;
            $fileCount = (int) ceil(count($models) / $urlsPerFile);
            $filenames = [];
            for ($i = 1; $i <= $fileCount; $i++){
                $filenames[] = $this->getStaticFileByFilename($section . "_part_" . $i);
            }



//            $filename = $section;
//            $sitemap = $this->getStaticFileByFilename($filename);


            dump($filenames);
        }
        return;



        $doc = new \DOMDocument();
        $doc->loadXML($sitemap->content);
        $parent = $doc->getElementsByTagName('urlset')->item(0);
        $url = $doc->createElement("url");
        $loc = $doc->createElement("loc", "https://legalbet.ru/best-posts/zhelto-sinij-trend-v-otbore-evro-2016/");
        $priority = $doc->createElement("priority", "0.5");
        $url->appendChild($loc);
        $url->appendChild($priority);
        $parent->appendChild($url);


        $xml = $doc->saveXML();


        $xml = simplexml_load_string($xml);
        if ($xml === false) {
            echo "Failed loading XML: ";
            foreach(libxml_get_errors() as $error) {
                echo "<br>", $error->message;
            }
        } else {
            d($xml);
        }



        return "";
    }

    /**
     * @TODO рефактор
     *
     * LB почти один в один COPYPAST
     * Блок генератора копии с легалбета
     */

    public function prepare()
    {
        $stat = $this->getRowsSectionCountStat();
        if(empty($stat)) {
            return;
        }

        $last_row = null;
        foreach($stat as $row){


            if($row['static_filename']){
                $last_row = $row;
                continue;
            }

            if(!$last_row || $last_row['section'] != $row['section']){
                // новая секция? сделать имя файла из секции
                $num = 1;
                $section = $row['section'];
            }elseif($last_row['count'] < SitemapGeneratorEnum::URLS_PER_FILE){
                $limit = SitemapGeneratorEnum::URLS_PER_FILE - $last_row['count'] + SitemapGeneratorEnum::URLS_PER_FILE_BOOST;
                dump($limit);
                $this->setFilenameBySection($row['section'], $last_row['static_filename'], $last_row['static_filename_part'], $limit);
                continue;
            }else{
                // новый файл, такой же как предыдущий но следующий номер partX
                $num = $last_row['static_filename_part'] + 1;
                $section = $row['section'];
            }

            if(!$section){
                $file_part_section = SitemapGeneratorEnum::DEFAULT_SECTION_NAME;
            }else{
                $file_part_section = str_replace(array('-', '.', ' '), '_', $section);
            }

            $filename = $file_part_section . '_part' . $num . '.' . FileExtensionEnum::XML;

            $this->setFilenameBySection($section, $filename, $num, SitemapGeneratorEnum::URLS_PER_FILE);
            $last_row = $row;
        }
    }

    /**
     * Обновляет данные по фаилу записей где не указан фаил
     *
     * @param $section
     * @param $filename
     * @param $static_filename_part
     * @param $limit
     * @return int|void
     */
    public function setFilenameBySection($section, $filename, $static_filename_part, $limit)
    {
        $models = $this->getAllBySection($section, null);
        if (empty($models)){
            return;
        }

        $ids = array_keys($models);

        return Sitemap::updateAll(
            [
                'static_filename' => $filename,
                'static_filename_part' => $static_filename_part,
            ],
            [
                'id' => $ids
            ]
        );
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

