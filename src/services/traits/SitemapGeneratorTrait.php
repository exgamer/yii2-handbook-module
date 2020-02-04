<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\SitemapGeneratorEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use concepture\yii2handbook\models\Sitemap;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\XmlHelper;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Trait SitemapGeneratorTrait
 * @package concepture\yii2handbook\services\traits
 */
trait SitemapGeneratorTrait
{
    /**
     * Новый xml документ
     * @param string $rootName
     * @return \DOMDocument
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

        return $doc;
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
        $models = $this->getAllByCondition(function (ActiveQuery $query) use ($section, $filename, $limit){
            $query->andWhere("static_filename IS NULL OR static_filename=''");
            $query->andWhere(['section' => $section]);
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
                'section' => $section
            ]);
            $query->indexBy('id');
            $query->limit($limit);
        });
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

    public function generate()
    {
        $this->prepare();
        /**
         *  получаем всю карту сайта из базы (только те урлы у которых уже указан файл)
         */
        $map = $this->getAllByCondition(function (ActiveQuery $query) {
            $query->andWhere("static_filename IS NOT NULL");
            $query->andWhere([
                'status' => StatusEnum::ACTIVE,
                'is_deleted' => IsDeletedEnum::NOT_DELETED,
            ]);
            $query->orderBy('static_filename ASC, id ASC');
            $query->asArray();
        });
        if (empty($map)){
            return;
        }

        $map[] = ClassHelper::modelToArray(new Sitemap());
        $urls = [];
        $section_files_count = $has_predefined = [];
        $last_key = null;
        $last_filename = false;
        $last_section = false;
        foreach($map as $row) {
            if((!isset($row['section']) || $row['section'] == '') && $row !== null){
                $has_predefined[] = $row['location'];
            }

            if ($last_section !== false && $last_filename != $row['static_filename']) {
                $result = $this->generateFile(array_reverse($urls),
                    $last_section,
                    $last_filename
                );
                if (! $result){
                    throw new \yii\db\Exception("file not saved");
                }

                $urls = [];
            }

            $last_section = $row['section'];
            $last_filename = $row['static_filename'];
            $urls[] = array(
                'location' => $row['location'],
                'last_modified_dt' => $row['last_modified_dt'],
                'priority' => $this->getPriority($row),
//                'changefreq' => '', // В легале оно было но не использовалось
                'id' => $row['id'],
                'static_filename' => $row['static_filename'],
                'section' => $row['section'],
            );


        }
//d($urls);
//        $this->generateIndexFile();
//
//        $predefined_url = $this->getPredefinedUrls();
//
//        if($predefined_url){
//            foreach($predefined_url as $url){
//                if(!in_array($url['loc'], $has_predefined)){
//                    $row = new Sitemap();
//                    $row->setLoc($url['loc']);
//                    $row->setSection('');
//                    $row->setChangefreq('daily');
//                    $dt = new \DateTime();
//                    $dt->setTimestamp(strtotime($url['lastmod']));
//                    $row->setLastmod($dt);
//                    $row->setObjectId(0);
//                    $row->setObjectType(null);
//                    $this->em->persist($row);
//                    $this->em->flush();
//                }
//            }
//        }

    }

    public function generateFile($urls, $section, $filename)
    {
        $host = Url::base(true);
        $document = $this->getNewDocument();
        foreach($urls as $row){
            $row['location'] = $host . $row['location'];
            $parent = $document->getElementsByTagName('urlset')->item(0);
            $url = $document->createElement("url");
            $loc = $document->createElement("loc", $row['location']);
            $priority = $document->createElement("priority", $row['priority']);
            $url->appendChild($loc);
            $url->appendChild($priority);
            $parent->appendChild($url);
        }

        return $this->saveFile($filename, $document->saveXML());
    }

    private function saveFile($filename, $content)
    {
        $currentFile = Yii::$app->staticFileService->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
            'filename' => $filename,
        ]);
        if (empty($currentFile)){
            $form = new StaticFileForm();
            $form->filename = $filename;
            $form->extension = FileExtensionEnum::XML;
            $form->is_hidden = 1;
            $form->content = $content;
            $form->type = StaticFileTypeEnum::SITEMAP;

            return Yii::$app->staticFileService->create($form);
        }

        if ($currentFile->content == $content){
            return true;
        }

        return Yii::$app->staticFileService->updateById($currentFile->id, ['content' => $content]);
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
        if (is_array($model)){
            $model = (object) $model;
        }

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

