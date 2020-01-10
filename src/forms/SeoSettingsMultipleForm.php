<?php

namespace concepture\yii2handbook\forms;

use Yii;
use yii\validators\Validator;
use yii\base\UnknownPropertyException;
use yii\helpers\ArrayHelper;
use kamaelkz\yii2admin\v1\forms\BaseModel;

/**
 * Форма редактирования SEO настроек, пачкой
 *
 * @todo возможно вынести в абстракцию
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoSettingsMultipleForm extends BaseModel
{
    /**
     * @var array
     */
    private $virtualAttributes = [];

    /**
     * @var array
     */
    public $ids = [];

    public function rules()
    {
        return [
            [
                [
                    'ids'
                ],
                'each',
                'rule' => [
                    'integer'
                ]
            ]
        ];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if(! property_exists($this, $name)) {
            $this->{$name} = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return ArrayHelper::merge(
            parent::attributes(),
            array_keys($this->virtualAttributes)
        );
    }

    /**
     * @param string $attribute
     * @param mixed $value
     */
    public function setVirtualAttribute($attribute, $value)
    {
        $attribute = $this->normalizeAttribute($attribute);
        $this->{$attribute} = $value;
        $this->virtualAttributes[$attribute] = $value;
    }

    /**
     * @param string $attribute
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function getVirtualAttribute($attribute)
    {
        if(! isset($this->virtualAttributes[$attribute])) {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $attribute);
        }

        $attribute = $this->normalizeAttribute($attribute);

        return $this->virtualAttributes[$attribute];
    }

    /**
     * Установка валидатора на виртуальный атрибут
     *
     * @param string $attribute
     * @param string $label
     */
    public function setRequiredValidator($attribute, $label)
    {
        $attribute = $this->normalizeAttribute($attribute);
        $validator = Validator::createValidator(
            'required',
            $this,
            [
                $attribute
            ],
            [
                'message' => Yii::t('yii', '{attribute} cannot be blank.' , ['attribute' => $label])
            ]
        );
        $this->getValidators()->append($validator);
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function normalizeAttribute($attribute)
    {
        return strtolower($attribute);
    }
}