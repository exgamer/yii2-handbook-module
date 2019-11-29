<?php
namespace concepture\yii2handbook\forms;


use Yii;
use concepture\yii2logic\forms\Form;

/**
 * Class SeoSettingsForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoSettingsForm extends Form
{
    public $domain_id;
    public $locale;
    public $url;
    public $url_md5_hash;
    public $seo_h1;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $seo_text;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'seo_h1',
                    'seo_title',
                    'seo_description'
                ],
                'required'
            ],
        ];
    }
}
