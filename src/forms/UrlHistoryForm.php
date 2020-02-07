<?php

namespace concepture\yii2handbook\forms;

use kamaelkz\yii2admin\v1\forms\BaseForm;
use concepture\yii2logic\enum\StatusEnum;

/**
 * Class UrlHistoryForm
 * @package concepture\yii2handbook\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UrlHistoryForm extends BaseForm
{
    public $domain_id;
    public $entity_type_id;
    public $entity_id;
    public $parent_id;
    public $location;

    /**
     * @inheritDoc
     */
    public function formRules()
    {
        return [
            [
                [
                    'location'
                ],
                'required'
            ],
        ];
    }
}
