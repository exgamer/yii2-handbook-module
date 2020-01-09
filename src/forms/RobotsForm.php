<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseForm;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Форма Индексного файла - robots.txt
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RobotsForm extends BaseForm
{
    public $domain_id;
    public $content;
    public $status = StatusEnum::INACTIVE;

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'content',
                ],
                'required'
            ],
        ];
    }
}
