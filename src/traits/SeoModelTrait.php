<?php

namespace concepture\yii2handbook\traits;

use Yii;
use concepture\yii2logic\validators\SeoNameValidator;
use concepture\yii2logic\validators\TranslitValidator;

/**
 * Трейт для расширения атрибутов моделей
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait SeoModelTrait
{
    public $seo_name;
    public $seo_h1;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;

    /**
     * Правила валидации по умолчанию
     *
     * @return array
     */
    public function seoRules()
    {
        return [
                    [
                        [
                            'seo_h1',
                            'seo_title',
                            'seo_description',
                            'seo_keywords',
                        ],
                        'string',
                        'max'=>175
                    ],
                    [
                        [
                            'seo_name',
                        ],
                        SeoNameValidator::class
                    ],
                    [
                        [
                            'seo_name',
                        ],
                        TranslitValidator::class,
                        'source' => $this->getSeoTranslationSource(),
                        'secondary_source' => $this->getSeoTranslationSourceSecond(),
                    ],
        ];
    }

    /**
     * Метки атрибутов
     *
     * @return array
     */
    public function seoAttributeLabels()
    {
        return [
            'seo_name' => Yii::t('handbook','SEO имя'),
            'seo_h1' => Yii::t('handbook','H1'),
            'seo_title' => Yii::t('handbook','Title'),
            'seo_description' => Yii::t('handbook','Description'),
            'seo_keywords' => Yii::t('handbook','Keywords'),
        ];
    }

    /**
     * @return string
     */
    protected function getSeoTranslationSource()
    {
        return 'seo_h1';
    }

    /**
     * @return string
     */
    protected function getSeoTranslationSourceSecond()
    {
        return 'title';
    }
}