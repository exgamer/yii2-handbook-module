<?php
namespace concepture\yii2handbook\models;

use concepture\yii2handbook\models\traits\DomainTrait;
use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\MD5Validator;

/**
 * Class SeoSettings
 * @package concepture\yii2handbook\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettings extends ActiveRecord
{
    use DomainTrait;

    public static function label()
    {
        return Yii::t('handbook', 'Настройки SEO');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{seo_settings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'domain_id',
                    'locale',
                ],
                'integer'
            ],
            [
                [
                    'seo_text',
                ],
                'string'
            ],
            [
                [
                    'seo_h1',
                    'url',
                ],
                'string',
                'max'=>1024
            ],
            [
                [
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                ],
                'string',
                'max'=>175
            ],
            [
                [
                    'url_md5_hash',
                ],
                MD5Validator::className(),
                'source' => 'url'
            ],
            [
                [
                    'locale',
                    'url_md5_hash'
                ],
                'unique',
                'targetAttribute' => ['locale', 'url_md5_hash']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('handbook','#'),
            'domain_id' => Yii::t('handbook','Домен'),
            'locale' => Yii::t('handbook','Язык'),
            'url' => Yii::t('handbook','url страницы'),
            'url_md5_hash' => Yii::t('handbook','md5 url страницы'),
            'seo_h1' => Yii::t('handbook','SEO H1'),
            'seo_title' => Yii::t('handbook','SEO title'),
            'seo_description' => Yii::t('handbook','SEO description'),
            'seo_keywords' => Yii::t('handbook','SEO keywords'),
            'seo_text' => Yii::t('handbook','SEO текст'),
            'created_at' => Yii::t('handbook','Дата создания'),
            'updated_at' => Yii::t('handbook','Дата обновления'),
        ];
    }
}
