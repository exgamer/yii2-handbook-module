<?php
namespace concepture\yii2handbook\models\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class PluralMessageBehavior
 *
 * 'PluralMessageBehavior' => [
 *     'class' => PluralMessageBehavior::class,
 *     'originText' => 'originPluralText',
 *     'pluralAttr' => 'plurals',
 * ],
 *
 * @package concepture\yii2handbook\models\behaviors
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class PluralMessageBehavior extends Behavior
{
    /**
     * @var string
     */
    public $token = '{plural}';
    /**
     * @var string
     */
    public $originText = null;
    /**
     * @var string
     */
    public $pluralAttr = null;
    /**
     * @var array
     */
    private $excludedTypes = [];

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'transform',
        ];
    }

    /**
     * Transforms plurals array to string
     */
    public function transform()
    {
        if (empty($this->pluralAttr)){
            return null;
        }

        $matches = [];
        preg_match_all('/[ ](\S\w*){(.*?)}/', $this->owner->{$this->originText}, $matches);

        if (count($matches) !== 3) {
            return null;
        }

        $types = $matches[1];
        $values = $matches[2];

        if (!$types) {
            return null;
        }

        if (isset($this->owner->{$this->pluralAttr}) && !empty($this->owner->{$this->pluralAttr})) {
            foreach ($this->owner->{$this->pluralAttr} as $target => $plural) {
                $replaceString = '';
                foreach ($types as $key => $type) {
                    $type = trim($type);
                    $value = isset($plural[$type]) ? $plural[$type] : null;
                    if (!$value) {
                        $value = $values[$key];
                    }
                    $replaceString .= "{$type}{{$value}} ";
                }

                if (strpos($this->owner->{$target}, $this->token) !== false) {
                    $replaceString = trim($replaceString);
                    $this->owner->{$target} = str_replace($this->token, "{n, plural, {$replaceString}}", $this->owner->{$target});
                } else {
                    if (! empty($this->owner->{$target})) {
                        $this->owner->addError($target, Yii::t('common', 'Необходимо указать токен {:token}', [
                            ':token' => $this->token,
                        ]));
                    }
                }
            }
        }
    }
}