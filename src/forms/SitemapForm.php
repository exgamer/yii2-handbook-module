<?php

namespace concepture\yii2handbook\forms;

use concepture\yii2handbook\enum\SitemapTypeEnum;
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
    public $static_filename;
    public $static_filename_part;
    public $controller_id;
    public $location;
    public $section;
    public $type = SitemapTypeEnum::DYNAMIC;
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
                    'controller_id',
                    'entity_id',
                    'location'
                ],
                'required'
            ],
        ];
    }
}
