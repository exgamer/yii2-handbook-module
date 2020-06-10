<?php

namespace concepture\yii2handbook\v2\dto;

use concepture\yii2logic\pojo\Pojo;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class DynamicElementDto extends Pojo
{
    public $name;
    public $key;
    public $caption;
    public $value;
    public $general;
    public $no_control;
    public $multi_domain;
}