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


## SEO настройки

- Для получения настроек сео для страницы вызываем
    ```php
   Yii::$app->seoSettingsService->getSeoDataSet($model = null);
    ```
   Метод вернет обьект  concepture\yii2handbook\datasets\SeoData
   в котором будут учтены:
    - дефолтные данные сео где не указан конкретный УРЛ
    - если передана модель следом будут учтены сео данные модели
    - и после всего будут учтены настройки SEO для текущего URL
        
- Расширение для твига - в конфигурацию твига добавить расширение:
    
    ```php
        ...
          'extensions' => 'concepture\yii2handbook\twig\SeoSettingsExtension'
        ...
    ```
- Пример
    ```twig
        {{ seo_setting(seo_constant('SettingsTypeEnum::TEXT'), seo_constant('SeoSettingEnum::TITLE'), 'Главная страница', 'Заголовок главной страницы') }}
        {{ seo_setting(seo_constant('SettingsTypeEnum::TEXT_AREA'), 'MY_TEXT_AREA', 'Произвольный текст', 'Некий текст') }}
        {{ seo_setting(seo_constant('SettingsTypeEnum::TEXT_EDITOR'), 'MY_EDITOR', 'Произвольный текст', 'Некий текст 2') }}
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
              'domainPatterns' => [
                  DomainEnum::A => 'objects',
                  DomainEnum::B => 'subjects',
              ],
              'route' => 'object/index'
            ],
            [
              'domainPatterns' => [
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