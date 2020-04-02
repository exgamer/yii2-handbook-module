<?php

namespace concepture\yii2handbook\forms;

use concepture\yii2logic\enum\StatusEnum;

/**
 * Форма сео блоков
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SeoBlockForm extends \kamaelkz\yii2admin\v1\forms\BaseForm
{
	public $caption; 
	public $position; 
	public $sort = 0; 
	public $content;
	public $status = StatusEnum::ACTIVE;
	public $domain_id;

    /**
     * @return array
     */
    public function formRules()
    {
        return [
            [
                [
                    'caption',
                    'position',
                    'content'
                ],
                'required'
            ],
            [
                [
                    'sort'
                ],
                'default',
                'value' => 0
            ],
        ];
    }
}