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
         'domain' => [
             'class' => 'concepture\yii2handbook\Module'
         ],
     ],





Получить id текущего домена из таблицы Yii::$app->domainService->getCurrentDomainId();