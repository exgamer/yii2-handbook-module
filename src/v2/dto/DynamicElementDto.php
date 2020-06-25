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
    public $value_params_keys;
    public $value_params_values;
    public $general;
    public $no_control;
    public $multi_domain;
    public $apply_unique_params;
}