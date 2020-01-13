<?php

namespace concepture\yii2handbook\datasets;

use yii\base\Model;
use concepture\yii2handbook\traits\VirtualAttributesTrait;

/**
 * Class SeoData
 * @package concepture\yii2handbook\datasets
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoData extends Model
{
    use VirtualAttributesTrait;

    public $seo_h1;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $seo_text;
}
