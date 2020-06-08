# Настройка интернационализации

> Прежде всего, вам необходимо создать конфигурационный файл.
  Решите где вы хотите хранить его и затем выполните команду

```bash
php yii message/config-template frontend/config/message.php
```

### Пример конфига
```php
return [
    'color' => null,
    'interactive' => true,
    'help' => null,
    'sourcePath' => '@frontend', // Откуда считывать ключи переводов
    'messagePath' => '@frontend/messages', // Куда будет сохраняться файл с переводами
    'languages' => ['ru'], // массив, содержащий языки,
                           // на которые ваше приложение должно быть переведено
    'translator' => ['Yii::t'],
    'sort' => false,
    'overwrite' => true,
    'removeUnused' => false, // Удаляет неиспользуемые переводы
    'markUnused' => true,
    'except' => [
        '.svn',
        '.git',
        '.gitignore',
        '.gitkeep',
        '.hgignore',
        '.hgkeep',
        '/messages',
        '/BaseYii.php',
        'runtime',
    ],
    'only' => [
        '*.php',
        '*.twig', // Нужно указать для считывания из twig файлов
    ],
    'format' => 'db', // Нужно указать для записи в базу данных
    'db' => 'db-translate', // Соединение с продом
    'useMoFile' => true, // Нужно указать для считывания переводов из .mo файлов
    'sourceMessageTable' => '{{%source_message}}', // таблица ключей переводов
    'messageTable' => '{{%message}}', // таблица переводов
    'catalog' => 'messages',
    'ignoreCategories' => [],
    'phpFileHeader' => '',
    'phpDocBlock' => null,
];
```
---

### Создание таблиц
```bash
yii migrate --migrationPath=@yii/i18n/migrations/
```

---

### Конфигурация приложения
> Для консольного приложения чтобы была возможность
  читать twig файлы, нужно добавить конфигурацию 'view'
```
'view' => [
    'class' => '\frontend\themes\components\View',
    'theme' => [
        'class'=> yii\base\Theme::class,
        'basePath'=>'@frontend/themes'
    ],
    'renderers' => [
        'twig' => require __DIR__ . './../../common/config/twig.php'
    ],
],
```
---
> Переопределен стандартный GettextMessageSource
  для установки локали из domainMap 
```
'i18n' => [
    'translations' => [
        '*' => [
            'class' => 'common\i18n\GettextMessageSource',
            'sourceLanguage' => AppHelper::getLocaleParam('language_iso'),
        ],
    ],
],
```

### Пример команд
```bash
# Импорт новых сообщений в базу данных
php yii message/import --alias=brru

# Экспорт переведенных сообщений из базы данных в .mo файл
php yii message/export --alias=brru

# Импорт в бд и экспорт из бд одновременно
php yii message/extract --alias=brru
```

### ENV
```dotenv
DB_TRANSLATE_HOST=translates_host
DB_TRANSLATE_PORT=3306
DB_TRANSLATE_NAME=project_name
DB_TRANSLATE_USERNAME=translate
DB_TRANSLATE_PASSWORD=password
DB_TRANSLATE_SCHEMA_CACHE=0
DB_TRANSLATE_SCHEMA_CACHE_DURATION=3600
```