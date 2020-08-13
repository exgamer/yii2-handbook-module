<?php

namespace concepture\yii2handbook\components\i18n;

use yii\base\Component;
use yii\base\Event;
use concepture\yii2handbook\traits\ServicesTrait as HandbookServicesTrait;

/**
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class TranslationHelper extends Component
{
    use HandbookServicesTrait;

    /**
     * @var array
     */
    private $callMessageStack = [];

    /**
     * @var array
     */
    private $ids = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        Event::on(GettextMessageSource::class, GettextMessageSource::EVENT_CALL_TRANSLATION, function (CallTranslationEvent $event) {
            $this->pushCallMessageStack($event->message);
        });
    }

    /**
     * @param string $message
     */
    public function pushCallMessageStack($message)
    {
        $hash = md5($message);
        if(isset($this->callMessageStack[$hash])) {
            return;
        }

        $this->callMessageStack[$hash] = $message;
    }

    /**
     * Очистка стэка вызова переводов
     */
    public function clearCallMessageStack()
    {
        $this->callMessageStack = [];
    }

    /**
     * Получение идентфикаторов переводов
     *
     * @return array|bool
     */
    public function getMessageIds()
    {
        if(! $this->callMessageStack) {
            return false;
        }

        # todo: неплохо было бы искать по хэшу, снимет нагрузку
        $messages = $this->sourceMessageService()->getAllByCondition(['message' => $this->callMessageStack]);
        if(! $messages) {
            return false;
        }

        $result = array_column($messages, 'id');
        unset($messages);
        $this->clearCallMessageStack();

        return $result;
    }
}