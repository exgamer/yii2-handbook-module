# concepture_engine

Модуль для разграничения контента по доменам.
Содержит сущность домена по которой можно разграничивать контент

    
Подключение

"require": {
    "concepture/yii2-handbook-module" : "*"
},
    

Миграции
 php yii migrate/up --migrationPath=@concepture/yii2handbook/console/migrations
 
Подключение модуля для админки

     'modules' => [
         'handbook' => [
             'class' => 'concepture\yii2handbook\Module'
         ],
     ],

Подключение модуля для консольного приложения

     'modules' => [
        'handbook' => [
            'class' => 'concepture\yii2handbook\Module',
            'controllerMap' => [
                'sitemap' => 'concepture\yii2handbook\console\controllers\SitemapController',
                'url-history' => 'concepture\yii2handbook\console\controllers\UrlHistoryController',
            ]
        ],
     ],
     
генерация карты саита
     
     php yii handbook/sitemap/generate 
     
полная перегенерация карты саита 

    php yii handbook/sitemap/re-generate
    
Для каждой сущности которая должна быть в карте саита в сервисе подключаем треит SitemapSupportTrait
и реализуем вызов метода sitemapRefresh в afterModelSave и afterDelete
    
!!! для получения карты саита в frontend/web должны лежать стили для xml

Генерация истории урлов для сущностей

    php yii handbook/url-history/re-generate
    
Для каждой сущности которая должна быть в истории в сервисе подключаем интерфеис UrlHistoryInterface
и реализуем вызов метода refresh в afterModelSave
    
    Yii::$app->urlHistoryService->refresh($model, null, 'site', 'page', ['route' => 'seo_name']);
     

     

Для работы с доменами в common/config/params-local.php добавить
 параметр в котором надо указать соответствие доменов (без http) к альясам из таблицы domains

```php
<?php

return [
    'yii2handbook' => [
        'domainMap' => [
            'example1.loc' => 'example1',
            'example2.loc' => 'example2'
        ]
    ]
];
```




Получить id текущей локали из таблицы  Yii::$app->localeService->getCurrentLocaleId()

Получить id текущего домена из таблицы Yii::$app->domainService->getCurrentDomainId();

Получить массив языков Yii::$app->localeService->catalog();

Получить массив доменов Yii::$app->domainService->catalog();

Получить настройку по ключу Yii::$app->settingsService->catalogValue($key);


## Динамические элементы

- Для получения настроек сео для страницы вызываем
    ```php
   Yii::$app->dynamicElementsService->getDataSet($model = null);
    ```
   Метод вернет обьект  concepture\yii2handbook\datasets\SeoData
   в котором будут учтены:
    - дефолтные данные сео где не указан конкретный УРЛ
    - если передана модель следом будут учтены сео данные модели
    - и после всего будут учтены элементы для текущего URL
        
- Расширение для твига - в конфигурацию твига добавить расширение:
    
    ```php
        ...
          'extensions' => 'concepture\yii2handbook\twig\DynamicElementsExtension'
        ...
    ```
- Пример
    ```twig
        {{ de(de_constant('SettingsTypeEnum::TEXT'), de_constant('SeoSettingEnum::TITLE'), 'Главная страница', 'Заголовок главной страницы') }}
        {{ de(de_constant('SettingsTypeEnum::TEXT_AREA'), 'MY_TEXT_AREA', 'Произвольный текст', 'Некий текст') }}
        {{ de(de_constant('SettingsTypeEnum::TEXT_EDITOR'), 'MY_EDITOR', 'Произвольный текст', 'Некий текст 2') }}
    ```
## Динамический индексируемый файл (robots.txt)
Подключение :
- В необходимом контроллере подключается действие (например frontend\controllers\SiteController.php)
    ```php
        public function actions()
        {
            $actions = parent::actions();
            ...
            $actions['robots'] = [
                'class' => 'concepture\yii2handbook\actions\RobotsAction',
            ];
            ...
        
            return $actions;
        }
    ```
- Прописать в роутинге правило (например frontend\config\routes.php)
    ```php
       return [
           ...
           [
               'pattern' => 'robots',
               'route' => 'site/robots',
               'suffix' => '.txt'
           ],
           ...
       ];
    ```
    
## Роутинг по доменам
- Подключение (например frontend\config\main.php)
    ```php
      ...
        'components' => [
            'urlManager' => [
                'class' => 'concepture\yii2handbook\components\routing\DomainUrlManager',
                'rules' => require __DIR__ . '/routes.php',
                ...
            ],
        ],
      ...
    ```
- Правила роутинга (например frontend\config\routes.php)
    ```php
          ...
            [
              'patterns' => [
                  DomainEnum::A => 'objects',
                  DomainEnum::B => 'subjects',
              ],
              'route' => 'object/index'
            ],
            [
              'patterns' => [
                  DomainEnum::A => "objects/<seo_name:({$seo_name_regexp})>",
                  DomainEnum::B => "subjects/<seo_name:({$seo_name_regexp})>",
              ],
              'route' => 'object/view'
            ],
            [
                'pattern' => 'robots',
                'route' => 'site/robots',
                'suffix' => '.txt'
            ]
          ...
    ```
## Hreflag для мультидоменов
- подключение в конфиге (напирмер frontend\config\main.php)
    ```php
    ...
        'view' => [
          ...
            'renderers' => [
                'twig' => [
                  ...
                      'extensions' => [
                          ...
                              'concepture\yii2handbook\components\routing\HreflangExtension'
                          ...
                      ],
                  ...

                ],
            ]
          ...
        ],    ...
    ```
- подключение на шаблоне (frontend\views\layout\main.html.twig)
```twig
    ...
    <head>
    ...
        {{ hreflang() }}
    ...
    </head>
    ...
```
- подключение доменов ( пример common/config/params-local.php ) параметр hreflang включает домен
```php
    return [
        'yii2handbook' => [
            'domainMap' => [
                'alias.loc' => [
                    'alias' => 'alias',
                    'locale' => 'ru',
                    'hreflang' => 'ru-ru'
                ]
            ]
        ]
    ];
```
## Модуль сортировки для контроллера
- Создать сущность object (Служебные сущности - таблица `entity_type`)
- Подключение в контроллере (пример backend\controllers\ObjectController)
```php
...
    	use concepture\yii2handbook\actions\PositionSortIndexAction;
    	use kamaelkz\yii2admin\v1\actions\EditableColumnAction;
    	use kamaelkz\yii2admin\v1\actions\SortAction;
    	use concepture\yii2handbook\services\EntityTypePositionSortService;
    	use kamaelkz\yii2admin\v1\controllers\traits\ControllerTrait;
...
    public function actions(): array
    {
        $actions = parent::actions();
    
        return array_merge($actions,[
            ...
            PositionSortIndexAction::actionName() => [
                'class' => PositionSortIndexAction::class,
                'entityColumns' => [
                    'id',
                    'name',
                    'seo_name',
                ],
                'labelColumn' => 'name',
            ],
            EditableColumnAction::actionName() => [
                'class' => EditableColumnAction::class,
                'serviceClass' => EntityTypePositionSortService::class
            ],
            SortAction::actionName() => [
                'class' => SortAction::class,
                'serviceClass' => EntityTypePositionSortService::class
            ]
            ...
        ]);
    }
...
```
- Подключение на вьюшке (backend\object\index.php)
```php
...
    use concepture\yii2handbook\actions\PositionSortIndexAction;
...
    $this->viewHelper()->pushPageHeader(
        [PositionSortIndexAction::actionName()],
        Yii::t('yii2admin', 'Сортировка'),
        'icon-sort'
    );
...
```

## Менеджер очередей Beanstalk
- Подключение (например common\config\main.php)
```php
  ...
    'components' => [
      ...
        'queue' => [
           'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueManager',
           'host' => '127.0.0.1', 
           'port' => 11300,
           'connectTimeout' => 1,
           'sleep' => false, 
           'enumClass' => 'common\enum\TubeEnum'
        ]
      ...
    ],
  ...
```
-Воркер должен быть унаследован от класса `concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker `
 и реализовывать его функции. Один воркер на одну трубу - (например `console\components\worker\TestWorker`)
 ```php
    namespace console\components\worker;
    
    use common\enum\TubeEnum;
    
    /**
     * Тестовый воркер
     *
     * @author kamaelkz <kamaelkz@yandex.kz>
     */
    class TestWorker extends \concepture\yii2handbook\components\queue\beanstalkd\worker\BaseWorker
    {
        /**
         * @inheritDoc
         */
        protected function getTubeName()
        {
            return TubeEnum::TEST;
        }
    
        /**
         * @inheritDoc
         */
        protected function execute($data)
        {
            //реализация логики обработки полезной нагрузки
        }
    }
 ```
- Подключение воркеров (например console\config\main.php)
```php
  ...
    'controllerMap' => [
        'worker:test' => [
            'class' => 'console\components\worker\TestWorker'
        ],
    ],
  ...
```
- Подключение консольной команды для работы с менеджером очередей (например console\config\main.php)
```php
  ...
    'controllerMap' => [
        'queue' => [
            'class' => 'concepture\yii2handbook\components\queue\beanstalkd\QueueCommand'
        ]
    ],
  ...
```