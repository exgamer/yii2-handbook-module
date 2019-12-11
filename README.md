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

```php
<?php

return [
    'yii2handbook' => [
        'domainMap' => [
            '1xbet.loc' => '1xbet',
            'bet365.loc' => 'bet365',
            'fonbet.loc' => 'fonbet',
            'winline.loc' => 'winline',
            'liga-stavok.loc' => 'ls',
        ]
    ]
];
```




Получить id текущей локали из таблицы  Yii::$app->localeService->getCurrentLocaleId()

Получить id текущего домена из таблицы Yii::$app->domainService->getCurrentDomainId();

Получить массив языков Yii::$app->localeService->catalog();

Получить массив доменов Yii::$app->domainService->catalog();

Получить настройку по ключу Yii::$app->settingsService->catalogValue($key);


SEO

Для получения настроек сео для страницы вызываем 
   Yii::$app->seoSettingsService->getSeoDataSet($model = null);
   Метод вернет обьект  concepture\yii2handbook\datasets\SeoData
   в котором будут учтены:
        - дефолтные данные сео где не указан конкретный УРЛ
        - если передана модель следом будут учтены сео данные модели
        - и после всего будут учтены настройки SEO для текущего URL