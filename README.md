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