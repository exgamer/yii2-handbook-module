<?php

namespace concepture\yii2handbook\console\controllers;

use yii\db\Query;
use yii\di\Instance;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Exception;
use yii\helpers\FileHelper;
use yii\i18n\GettextPoFile;
use common\helpers\AppHelper;
use yii\base\InvalidConfigException;
use Symfony\Component\Process\Process;
use yii\console\controllers\MessageController as Base;

/**
 * Class MessageController
 * @package console\controllers
 */
abstract class MessageController extends Base
{
    /**
     * @var string
     */
    public $configFile;

    /**
     * Initialization
     */
    public function init()
    {
        if (!$this->configFile) {
            $this->configFile = '@frontend/config/message.php';
        }
        // Для сообщений используем соединение с базой продакшена
        // Компонент создается здесь, так как нигде больше использоваться не должен
        // Нужно добавить в .env
        \Yii::$app->setComponents([
            'db-translate' => [
                'class' => 'yii\db\Connection',
                'dsn' => 'mysql:host=' . getenv('DB_TRANSLATE_HOST') . ';dbname=' . getenv('DB_TRANSLATE_NAME'),
                'username' => getenv('DB_TRANSLATE_USERNAME'),
                'password' => getenv('DB_TRANSLATE_PASSWORD'),
                'charset' => 'utf8',
                'enableSchemaCache' => getenv('DB_TRANSLATE_SCHEMA_CACHE'),
                'schemaCacheDuration' => getenv('DB_TRANSLATE_SCHEMA_CACHE_DURATION'),
                'schemaCache' => 'cache'
            ],
        ]);
        parent::init();
    }

    /**
     * Импорт новых сообщений в базу данных
     *
     * @param string $configFile
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionImport($configFile = null )
    {
        if (!$configFile) {
            $configFile = $this->configFile;
        }

        $this->initConfig($configFile);
        $this->importToDb();
    }

    /**
     * Экспорт переведенных сообщений из базы данных в .mo файл
     *
     * @param string $configFile
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionExport($configFile = null)
    {
        if (!$configFile) {
            $configFile = $this->configFile;
        }

        $this->initConfig($configFile);
        $this->exportFromDb();
    }

    /**
     * Импорт в бд и экспорт из бд одновременно
     *
     * @param null $configFile
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionExtract($configFile = null)
    {
        if (!$configFile) {
            $configFile = $this->configFile;
        }

        $this->initConfig($configFile);

        if ($this->config['format'] !== 'db') {
            parent::actionExtract($configFile);
        }

        $this->importToDb();
        $this->exportFromDb();
    }

    /**\
     * @param string $fileName
     * @param string $translator
     * @param array $ignoreCategories
     * @return array
     * @throws InvalidConfigException
     */
    protected function extractMessages($fileName, $translator, $ignoreCategories = [])
    {
        $this->stdout('Extracting messages from ');
        $this->stdout($fileName, Console::FG_CYAN);
        $this->stdout("...\n");

        $subject = file_get_contents($fileName);
        if (pathinfo($fileName)['extension'] == 'twig') {
            $renderer = \Yii::createObject(\Yii::$app->view->renderers['twig']);
            $twig = $renderer->twig;
            $twig->setLoader(new \Twig\Loader\ArrayLoader(array('fff' => file_get_contents($fileName))));
            $stream = $twig->tokenize(new \Twig\Source(file_get_contents($fileName), 'fff'));
            $nodes = $twig->parse($stream);
            $subject = $twig->compile($nodes);
        }

        $messages = [];
        $tokens = token_get_all($subject);

        foreach ((array) $translator as $currentTranslator) {
            $translatorTokens = token_get_all('<?php ' . $currentTranslator);
            array_shift($translatorTokens);
            $messages = array_merge_recursive($messages, $this->extractMessagesFromTokens($tokens, $translatorTokens, $ignoreCategories));
        }

        $this->stdout("\n");

        return $messages;
    }

    /**
     * @throws InvalidConfigException
     */
    private function importToDb()
    {
        $files = FileHelper::findFiles(realpath($this->config['sourcePath']), $this->config);
        $this->includeFiles($files);

        $messages = [];
        foreach ($files as $file) {
            $messages = array_merge_recursive($messages, $this->extractMessages($file, $this->config['translator'], $this->config['ignoreCategories']));
        }

        $catalog = isset($this->config['catalog']) ? $this->config['catalog'] : 'messages';

        /** @var Connection $db */
        $db = Instance::ensure($this->config['db'], Connection::className());
        $sourceMessageTable = isset($this->config['sourceMessageTable']) ? $this->config['sourceMessageTable'] : '{{%source_message}}';
        $messageTable = isset($this->config['messageTable']) ? $this->config['messageTable'] : '{{%message}}';
        $this->saveMessagesToDb(
            $messages,
            $db,
            $sourceMessageTable,
            $messageTable,
            $this->config['removeUnused'],
            $this->config['languages'],
            $this->config['markUnused']
        );
    }

    /**
     * Включает исключенные файлы или директории указанные в конфиге
     * @param array $files
     */
    private function includeFiles(&$files)
    {
        if (isset($this->config['include']) && !empty($this->config['include'])) {
            $includeFiles = [];
            foreach ($this->config['include'] as $path) {
                $includeFiles = ArrayHelper::merge($includeFiles, FileHelper::findFiles($path, $this->config));
            }
            if ($includeFiles) {
                $files = ArrayHelper::merge($files, $includeFiles);
            }
        }
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    private function exportFromDb()
    {
        /** @var Connection $db */
        $db = Instance::ensure($this->config['db'], Connection::className());

        $catalog = isset($this->config['catalog']) ? $this->config['catalog'] : 'messages';
        $mainQuery = (new Query())->select(['message' => 't1.message', 'category' => 't1.category', 'language' => 't2.language', 'translation' => 't2.translation'])
            ->from(['t1' => $this->sourceMessageTable])->innerJoin(['t2' => $this->messageTable], 't1.id = t2.id');

        $langMessages = [];
        foreach ($mainQuery->batch(100, $db) as $existingMessage) {
            foreach ($existingMessage as $message) {
                $langMessages[$message['language']][$message['category']]['keys'][] = $message['message'];
                $langMessages[$message['language']][$message['category']]['values'][$message['message']] = $message['translation'];
            }
        }

        foreach ($langMessages as $language => $messages) {
            if (!in_array($language, $this->config['languages'])) {
                continue;
            }
            $dir = $this->config['messagePath'] . DIRECTORY_SEPARATOR . $language;
            if (!is_dir($dir) && !@mkdir($dir)) {
                throw new Exception("Directory '{$dir}' can not be created.");
            }

            $this->saveMessagesToPO($messages, $dir, $this->config['overwrite'], $this->config['removeUnused'], $this->config['sort'], $catalog, $this->config['markUnused']);
        }

        foreach ($langMessages as $language => $messages) {
            if (!in_array($language, $this->config['languages'])) {
                continue;
            }
            
            $dir = $this->config['messagePath'] . DIRECTORY_SEPARATOR . $language;
            $file = str_replace('\\', '/', "$dir/$catalog.po");
            $this->convertPoToMoFile($file, $dir, $catalog);
            unlink($file);
        }
    }

    /**
     * @param array $messages
     * @param string $dirName
     * @param bool $overwrite
     * @param bool $removeUnused
     * @param bool $sort
     * @param string $catalog
     * @param bool $markUnused
     * @throws \yii\base\Exception
     */
    protected function saveMessagesToPO($messages, $dirName, $overwrite, $removeUnused, $sort, $catalog, $markUnused)
    {
        if (!$this->config['format'] === 'db') {
            parent::saveMessagesToPO($messages, $dirName, $overwrite, $removeUnused, $sort, $catalog, $markUnused);
        }

        $file = str_replace('\\', '/', "$dirName/$catalog.po");
        FileHelper::createDirectory(dirname($file));
        $this->stdout("Saving messages to $file...\n");

        $poFile = new GettextPoFile();

        $merged = [];
        $todos = [];

        $hasSomethingToWrite = false;
        foreach ($messages as $category => $msgs) {
            $translations = $msgs['values'];
            $msgs = $msgs['keys'];

            $notTranslatedYet = [];
            $msgs = array_values(array_unique($msgs));

            if (is_file($file)) {
                $existingMessages = $poFile->load($file, $category);

                sort($msgs);
                ksort($existingMessages);
                if (array_keys($existingMessages) == $msgs) {
                    $this->stdout("Nothing new in \"$category\" category...\n");

                    sort($msgs);
                    foreach ($msgs as $message) {
                        // TODO перепроверить
//                        $merged[$category . chr(4) . $message] = $existingMessages[$message];
                        $merged[$category . chr(4) . $message] = $translations[$message];
                    }
                    ksort($merged);
                    continue;
                }

                // merge existing message translations with new message translations
                foreach ($msgs as $message) {
                    if (array_key_exists($message, $existingMessages) && $existingMessages[$message] !== '') {
                        // TODO перепроверить
//                        $merged[$category . chr(4) . $message] = $existingMessages[$message];
                        $merged[$category . chr(4) . $message] = $translations[$message];
                    } else {
                        $notTranslatedYet[] = $message;
                    }
                }
                ksort($merged);
                sort($notTranslatedYet);

                // collect not yet translated messages
                foreach ($notTranslatedYet as $message) {
                    $todos[$category . chr(4) . $message] = '';
                }

                // add obsolete unused messages
                foreach ($existingMessages as $message => $translation) {
                    if (!$removeUnused && !isset($merged[$category . chr(4) . $message]) && !isset($todos[$category . chr(4) . $message])) {
                        if (!$markUnused || (!empty($translation) && (substr($translation, 0, 2) === '@@' && substr($translation, -2) === '@@'))) {
                            $todos[$category . chr(4) . $message] = $translation;
                        } else {
                            $todos[$category . chr(4) . $message] = '@@' . $translation . '@@';
                        }
                    }
                }

                $merged = array_merge($merged, $todos);
                if ($sort) {
                    ksort($merged);
                }

                if ($overwrite === false) {
                    $file .= '.merged';
                }
            } else {
                sort($msgs);
                foreach ($msgs as $message) {
                    // TODO перепроверить
//                    $merged[$category . chr(4) . $message] = '';
                    $merged[$category . chr(4) . $message] = $translations[$message];
                }
                ksort($merged);
            }
            $this->stdout("Category \"$category\" merged.\n");
            $hasSomethingToWrite = true;
        }
//        if ($hasSomethingToWrite) {
            $poFile->save($file, $merged);
            $this->stdout("Translation saved.\n", Console::FG_GREEN);
//        } else {
//            $this->stdout("Nothing to save.\n", Console::FG_GREEN);
//        }
    }

    /**
     * @param string $poFile
     * @param string $dirName
     * @param string $catalog
     * @throws \Exception
     */
    private function convertPoToMoFile($poFile, $dirName, $catalog)
    {
        $moFile = str_replace('\\', '/', "{$dirName}/{$catalog}.mo");

        $options = implode(' ', [
            '--check',
            "-o $moFile",
            $poFile,
        ]);

        $process = new Process('msgfmt ' . $options);
        $process->run();
        $output = $process->getOutput();
        if (!$process->isSuccessful()){
            throw new \Exception($process->getErrorOutput());
        }
    }

    /**
     * Переопределено для обновления неиспользуемых сообщений если они снова используются
     * @inheritDoc
     */
    protected function saveMessagesToDb($messages, $db, $sourceMessageTable, $messageTable, $removeUnused, $languages, $markUnused)
    {
        $currentMessages = [];
        $rows = (new Query())->select(['id', 'category', 'message'])->from($sourceMessageTable)->all($db);
        foreach ($rows as $row) {
            $currentMessages[$row['category']][$row['id']] = $row['message'];
        }

        $currentLanguages = [];
        $rows = (new Query())->select(['language'])->from($messageTable)->groupBy('language')->all($db);
        foreach ($rows as $row) {
            if (!in_array($row['language'], $languages)) {
                continue;
            }
            $currentLanguages[] = $row['language'];
        }
        $missingLanguages = [];
        if (!empty($currentLanguages)) {
            $missingLanguages = array_diff($languages, $currentLanguages);
        }

        $new = [];
        $obsolete = [];
        // Массив сообщений которые были помечены как неиспользуемые ранее
        // но снова используются
        $toUpdate = [];
        foreach ($messages as $category => $msgs) {
            $msgs = array_unique($msgs);

            if (isset($currentMessages[$category])) {
                // Помеченные как неиспользуемые, но снова использующиеся
                $toUpdate += array_filter(
                    array_map(function ($value) use ($msgs) {
                        return strpos($value, '@@') !== false && in_array(trim($value, '@@'), $msgs)
                            ? trim($value, '@@')
                            : null;
                    }, $currentMessages[$category])
                );

                $new[$category] = array_diff($msgs, $currentMessages[$category], $toUpdate);
                $obsolete += array_diff($currentMessages[$category], $msgs, $toUpdate);
            } else {
                $new[$category] = $msgs;
            }
        }

        foreach (array_diff(array_keys($currentMessages), array_keys($messages)) as $category) {
            $obsolete += $currentMessages[$category];
        }

        // Если есть вновь используемые сообщения
        if ($toUpdate) {
            // Убираем их из устаревших
            $obsolete = array_filter(
                array_map(function ($value) use ($toUpdate) {
                    return in_array(trim($value, '@@'), $toUpdate) ? $value : null;
                }, $obsolete)
            );

            $rows = (new Query())
                ->select(['id', 'message'])
                ->from($sourceMessageTable)
                ->where(['in', 'id', array_keys($toUpdate)])
                ->all($db);

            foreach ($rows as $row) {
                $db->createCommand()->update(
                    $sourceMessageTable,
                    ['message' => trim($row['message'], '@@')],
                    ['id' => $row['id']]
                )->execute();
            }
            $this->stdout("Obsolete updated.\n");
        }

        if (!$removeUnused) {
            foreach ($obsolete as $pk => $msg) {
                if (mb_substr($msg, 0, 2) === '@@' && mb_substr($msg, -2) === '@@') {
                    unset($obsolete[$pk]);
                }
            }
        }

        $obsolete = array_keys($obsolete);
        $this->stdout('Inserting new messages...');
        $savedFlag = false;

        foreach ($new as $category => $msgs) {
            foreach ($msgs as $msg) {
                $savedFlag = true;
                $lastPk = $db->schema->insert($sourceMessageTable, ['category' => $category, 'message' => $msg]);
                foreach ($languages as $language) {
                    $db->createCommand()
                        ->insert($messageTable, ['id' => $lastPk['id'], 'language' => $language])
                        ->execute();
                }
            }
        }

        if (!empty($missingLanguages)) {
            $updatedMessages = [];
            $rows = (new Query())->select(['id', 'category', 'message'])->from($sourceMessageTable)->all($db);
            foreach ($rows as $row) {
                $updatedMessages[$row['category']][$row['id']] = $row['message'];
            }
            foreach ($updatedMessages as $category => $msgs) {
                foreach ($msgs as $id => $msg) {
                    $savedFlag = true;
                    foreach ($missingLanguages as $language) {
                        $db->createCommand()
                            ->insert($messageTable, ['id' => $id, 'language' => $language])
                            ->execute();
                    }
                }
            }
        }

        // Проверяем сохраненные ранее сообщения на наличие всех языков
        $missingMessageLangs = (new Query())->select(['id', 'count(id) as count', 'GROUP_CONCAT(language SEPARATOR ",") as languages'])
            ->from($messageTable)->groupBy('id')
            ->all($db);

        if (!empty($missingMessageLangs)) {
            $toInsert = [];
            foreach ($missingMessageLangs as $data) {
                $messageLanguages = explode(',', $data['languages']);
                $missing = array_diff($languages, $messageLanguages);
                if (!$missing) {
                    continue;
                }

                foreach ($missing as $lang) {
                    $toInsert[] = [
                        'id' => $data['id'],
                        'language' => $lang,
                    ];
                }
            }
            
            if ($toInsert) {
                $db->createCommand()
                    ->batchInsert($messageTable, ['id', 'language'], $toInsert)
                    ->execute();
            }
        }

        $this->stdout($savedFlag ? "saved.\n" : "Nothing to save.\n");
        $this->stdout($removeUnused ? 'Deleting obsoleted messages...' : 'Updating obsoleted messages...');

        if (empty($obsolete)) {
            $this->stdout("Nothing obsoleted...skipped.\n");
            return;
        }

        if ($removeUnused) {
            $db->createCommand()
                ->delete($sourceMessageTable, ['in', 'id', $obsolete])
                ->execute();
            $this->stdout("deleted.\n");
        } elseif ($markUnused) {
            $rows = (new Query())
                ->select(['id', 'message'])
                ->from($sourceMessageTable)
                ->where(['in', 'id', $obsolete])
                ->all($db);

            foreach ($rows as $row) {
                $db->createCommand()->update(
                    $sourceMessageTable,
                    ['message' => '@@' . $row['message'] . '@@'],
                    ['id' => $row['id']]
                )->execute();
            }
            $this->stdout("updated.\n");
        } else {
            $this->stdout("kept untouched.\n");
        }
    }
}