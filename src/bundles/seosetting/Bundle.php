<?php

namespace concepture\yii2handbook\bundles\seosetting;

use concepture\yii2logic\bundles\Bundle as CoreBundle;

/**
 * Бандл SEO настроек
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Bundle extends CoreBundle
{
    public $js = [
        'js/script.js'
    ];

    public $css = [
        'css/style.css'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}