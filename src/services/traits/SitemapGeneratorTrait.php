<?php
namespace concepture\yii2handbook\services\traits;

use concepture\yii2handbook\enum\FileExtensionEnum;
use concepture\yii2handbook\enum\SitemapGeneratorEnum;
use concepture\yii2handbook\enum\SitemapTypeEnum;
use concepture\yii2handbook\enum\StaticFileTypeEnum;
use concepture\yii2handbook\forms\StaticFileForm;
use concepture\yii2handbook\models\Sitemap;
use concepture\yii2handbook\services\interfaces\SitemapServiceInterface;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\XmlHelper;
use concepture\yii2logic\models\traits\HasLocalizationTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2logic\models\traits\v2\property\HasLocalePropertyTrait;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Url;

/**
 * Trait SitemapGeneratorTrait
 * @package concepture\yii2handbook\services\traits
 */
trait SitemapGeneratorTrait
{
    /**
     * Перегенерация карты саита с нуля
     * Для работы сервис должен использовать SitemapSupportTrait
     * @param string $scheme
     * @throws Exception
     */
    public function regenerate($scheme = 'https')
    {
        $this->outputSuccess('regenerate sitemap start');
        Sitemap::deleteAll([
            'domain_id' => $this->domainService()->getCurrentDomainId(),
            'type'=> SitemapTypeEnum::DYNAMIC
        ]);
        $this->staticFileService()->clearSiteMaps();
        $entities = $this->entityTypeService()->catalog('id', 'table_name');
        foreach ($entities as $entity){
            $service = $this->getServiceByEntityTable($entity);
            if (! $service){
                continue;
            }

            $traits = ClassHelper::getTraits($service);
            if (! in_array(SitemapSupportTrait::class, $traits) && ! $service instanceof SitemapServiceInterface){
//                $this->outputDone('skip regenerating sitemap for ' . $entity);
                continue;
            }
            $this->outputSuccess('regenerate sitemap for ' . $entity);
            /**
             * @todo тут надо заменить выборку всего на использование  concepture\yii2logic\dataprocessor\DataProcessor
             * иначе на больших данных умрет
             */
            $models = $service->getAllbyCondition(function(ActiveQuery $query) use ($service){
                $query->active();
                $query->notDeleted();
            });

            if (empty($models)){
                continue;
            }

            $count = count($models);
            Console::startProgress(0, $count);
            foreach ($models as $k => $model){
                $service->updateById($model->id, [], '', false);
                Console::updateProgress($k + 1 , $count);
            }
        }

        $this->generate($scheme);
        $this->outputSuccess('regenerate sitemap done');
    }

    /**
     * Новый xml документ
     * @param string $rootName
     * @param string $styleFilePath
     * @return \DOMDocument
     */
    public function getNewDocument($rootName = 'urlset', $styleFilePath = "/sitemap.xsl")
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
        $xslt = $doc->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="' .$styleFilePath. '"');
        $doc->appendChild($xslt);
        $child = XmlHelper::generateXmlElement( $doc, $data );
        if ( $child )
            $doc->appendChild( $child );
        $doc->formatOutput = true; // Add whitespace to make easier to read XML

        return $doc;
    }

    /**
     * @TODO рефактор
     *
     * LB логика отрефакторенный COPYPAST с легал бета
     * Блок генератора копии с легалбета
     */

    /**
     * Получает данные по sitemap генерит названия файлов
     * и записывает
     */
    public function prepare()
    {
        $stat = $this->getRowsSectionCountStat();
        if(empty($stat)) {
            return;
        }
        Yii::info(count($stat) . ' sections found', 'sitemap_generator');

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

    /**
     * Генерация карты саита
     *
     * @param string $scheme
     * @throws Exception
     */
    public function generate($scheme = 'https')
    {
        Yii::info('Sitemap generate start...', 'sitemap_generator');
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
                    $last_filename,
                    $scheme
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

        $this->generateIndexFile($scheme);

        Yii::info('Sitemap generate end...', 'sitemap_generator');
    }

    /**
     * Генерация индексного файла карты саита
     * @param string $scheme
     */
    public function generateIndexFile($scheme = 'https')
    {
        $document = $this->getNewDocument('sitemapindex', "/sitemapindex.xsl");
        $files = $this->staticFileService()->getSitemapIndexList();
        foreach($files as $row){
            $location = $this->getSitemapAbsoluteUrl("/sitemap/".$row['filename'] . "." . $row['extension'], $scheme);
            $parent = $document->getElementsByTagName('sitemapindex')->item(0);
            $sitemap = $document->createElement("sitemap");
            $loc = $document->createElement("loc", $location);
            $date = Yii::$app->formatter->asDateTime($row['last_modified_dt'], 'php:Y-m-d\TH:iP');
            $lastmod = $document->createElement("lastmod", $date);
            $sitemap->appendChild($loc);
            $sitemap->appendChild($lastmod);
            $parent->appendChild($sitemap);
        }

        $this->saveFile('index.xml', $document->saveXML(), StaticFileTypeEnum::SITEMAP_INDEX);
        Yii::info('index file generated', 'sitemap_generator');
    }

    /**
     * Генерация фаила карты саита
     * @param $urls
     * @param $section
     * @param $filename
     * @param string $scheme
     * @return bool
     */
    public function generateFile($urls, $section, $filename, $scheme = 'https')
    {
        $max_date = '';
        $document = $this->getNewDocument();
        foreach($urls as $row){
            $max_date = $row['last_modified_dt'] > $max_date ? $row['last_modified_dt'] : $max_date;
            $location = $this->getSitemapAbsoluteUrl($row['location'], $scheme);
            $parent = $document->getElementsByTagName('urlset')->item(0);
            $url = $document->createElement("url");
            $loc = $document->createElement("loc", $location);
            $priority = $document->createElement("priority", $row['priority']);
            $url->appendChild($loc);
            $url->appendChild($priority);
            $parent->appendChild($url);
        }

        return $this->saveFile($filename, $document->saveXML(), StaticFileTypeEnum::SITEMAP, $max_date);
    }

    /**
     * Сохранение фаила карты саита
     *
     * @param $filename
     * @param $content
     * @param int $type
     * @param null $last_modified_dt
     * @return bool
     */
    private function saveFile($filename, $content ,$type = StaticFileTypeEnum::SITEMAP, $last_modified_dt = null)
    {
        $filename = str_replace("." . FileExtensionEnum::XML, "", $filename);
        $currentFile = Yii::$app->staticFileService->getOneByCondition([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED,
            'filename' => $filename,
            'extension' => FileExtensionEnum::XML,
            'type' => $type,
        ]);

        if (empty($currentFile)){
            $form = new StaticFileForm();
            $form->filename = $filename;
            $form->extension = FileExtensionEnum::XML;
            $form->is_hidden = 1;
            $form->content = $content;
            $form->type = $type;
            $form->last_modified_dt = $last_modified_dt;

            return Yii::$app->staticFileService->create($form);
        }

        if ($currentFile->content == $content){
            return true;
        }

        $updateParams = ['content' => $content];
        if ($last_modified_dt){
            $updateParams['last_modified_dt'] = $last_modified_dt;
        }

        return Yii::$app->staticFileService->updateById($currentFile->id, $updateParams);
    }

    /**
     * Возвращает абсолютную ссылку на саитмап
     *
     * @param $location
     * @param string $scheme
     * @return string
     */
    protected function getSitemapAbsoluteUrl($location, $scheme = 'https')
    {
        $host = $this->domainService()->getCurrentHost();
        $parsed = parse_url($host);
        if (empty($parsed['scheme'])) {
            $host = $scheme . '://' . ltrim($host, '/');
        }

        return  $host  . $location;
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