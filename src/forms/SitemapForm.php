<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseForm;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class SitemapForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SitemapForm extends BaseForm
{
    public $domain_id;
    public $entity_type_id;
    public $entity_id;
    public $static_file_id;
    public $location;
    public $section;
    public $last_modified_dt;
    public $status = StatusEnum::ACTIVE;

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'entity_type_id',
                    'entity_id',
                    'location',
                    'section',
                ],
                'required'
            ],
        ];
    }
}
