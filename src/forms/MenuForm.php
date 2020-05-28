<?php

namespace concepture\yii2handbook\forms;

use yii\validators\UrlValidator;
use kamaelkz\yii2admin\v1\forms\BaseForm;

/**
 * Class MenuForm
 *
 * @package concepture\yii2handbook\forms
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class MenuForm extends BaseForm
{
    /** @var int */
    public $type;
    /** @var string */
    public $caption;
    /** @var string */
    public $url;
    /** @var int */
    public $desktop_max_count;
    /** @var string */
    public $link_all_caption;
    /** @var string */
    public $link_all_url;
    /** @var array */
    public $items = [];
    /** @var int */
    public $domain_id;
    /** @var int */
    public $status = 0;

    /**
     * @return array
     */
    public function formRules()
    {
        return [
            [
                [
                    'type',
                    'caption',
                ],
                'required',
            ],
            [
                [
                    'desktop_max_count',
                ],
                'integer',
            ],
            [
                [
                    'caption',
                    'link_all_caption',
                ],
                'string',
                'max' => 255,
            ],
            [
                [
                    'url',
                    'link_all_url',
                ],
                'string',
                'max' => 1024,
            ],
//            [
//                [
//                    'url',
//                ],
//                UrlValidator::class,
//                'pattern' => '/^\/(([A-Z0-9][A-Z0-9_-]*)/i',
//            ],
            [
                [
                    'items',
                ],
                'safe',
            ],
        ];
    }
}
