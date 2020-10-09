<?php

namespace concepture\yii2handbook\components\multilanguage;

use Yii;
use concepture\yii2handbook\components\cache\CacheWidget;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class MultiLanguageWidget extends CacheWidget
{
    use LanguageServiceTrait;

    /**
     * @var string
     */
    public $language;

    /**
     * @inheritDoc
     */
    public function beforeRun()
    {
        if(Yii::$app instanceof \yii\web\Application) {
            $this->language = $this->getLanguageService()->getCurrent();
        } else {
            $this->language = $this->options['language'];
            $this->getLanguageService()->setCurrent($this->language);
        }

        return parent::beforeRun();
    }
}