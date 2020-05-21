# гаид по сущностям которые привязаны по связочной таблице
#структура перевязочной таблицы
     'entity_type_id',
     'entity_id',
     '<название связанной таблицы>_id',
     'status',
     'sort',
     
0. Для перевязочной таблицы создаем модель, форму и сервис     

1. Пример Модели к которой привязаны категории постов черезе таблицу entity_post_category

```php

<?php

namespace common\models;

use concepture\yii2article\models\PostCategory;
use Yii;
use yii\helpers\ArrayHelper;
use concepture\yii2handbook\models\Country as Base;

/**
 * Страны DI
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Country extends Base
{
    /**
     * @var PostCategory[]
     */
    public $post_categories;

    public function behaviors()
    {
        return [
            'JsonFieldsBehavior' => [
                'class' => 'concepture\yii2handbook\models\behaviors\LinkedEntityBehavior',
                'linkAttr' => [
                    'post_categories' => [
                        'class' => PostCategory::class,
                        'link_class' => EntityPostCategory::class,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return 
            [
                [
                    [
                        'post_categories',
                    ],
                    'safe'
                ]
            ];
    }
}


```

2. Пример формы 

```php

<?php

namespace common\forms;

use Yii;
use yii\helpers\ArrayHelper;
use concepture\yii2handbook\forms\CountryForm as Base;

/**
 * Форма страны DI
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class CountryForm extends Base
{
    public $post_categories;
}


```

3. Пример вывода на форме

```php
    <legend class="font-weight-semibold text-uppercase font-size-sm">
        <?= Yii::t('yii2admin', 'Категории постов')?>
        <?= HintWidget::widget(['name' => "{$model->underscoreFormName()}_header_post_category"]);?>
    </legend>
    <?= DynamicForm::widget([
        // 'limit' => 20, // the maximum times, an element can be cloned (default 999)
        'min' => empty($model->post_categories) ? 0 :1, // 0 or 1 (default 1)
        'form' => $form,
        'models' =>  $originModel->getLinkModels('post_categories'),
        'dragAndDrop' => true,
        'formId' => $form->getId(),
        'editable' => false,
        'attributes' => [
            'name' => function($model, $form, $key, $value) {
                $sportIdInput = Html::hiddenInput(
                    'CountryForm[post_categories]['.$key.'][link_id]',
                    $model->link_id,
                    ['value' => $model->link_id,]
                );
                return $sportIdInput . $model->name;
            },
            'status' => function($model, $form, $key, $value) {
                return Html::checkbox(
                    'CountryForm[post_categories]['.$key.'][status]',
                    $value,
                    ['class' => 'form-check-input-styled-primary',]
                );
            },
        ]
    ]); ?>
```