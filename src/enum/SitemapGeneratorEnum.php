<?php

namespace concepture\yii2handbook\enum;

use Yii;
use concepture\yii2logic\enum\Enum;

/**
 * Class SitemapGeneratorEnum
 * @package concepture\yii2handbook\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapGeneratorEnum extends Enum
{
    const URLS_PER_FILE = 5000;
    const URLS_PER_FILE_BOOST = 500;
}
