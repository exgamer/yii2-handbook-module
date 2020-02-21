<?php

namespace concepture\yii2handbook\components\routing;

use Yii;
use concepture\yii2logic\services\Service;
use yii\helpers\Html;

/**
 * Сервис формирования альтарнативных адресов страниц по локалям
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangService extends Service
{
    /**
     * @var bool признак активности
     */
    private $_active = true;

    /**
     * Включение вывода элементов
     */
    public function enable()
    {
        $this->_active = true;
    }

    /**
     * Выключение вывода элементов
     */
    public function disable()
    {
        $this->_active = false;
    }

    /**
     * Список локалей для отображения элементов
     *
     * @param array $array
     */
    public function allowedLocales($items)
    {
        # todo : реализовать
    }

    /**
     * Возвращает сформированные HTML тэги
     *
     * @return string|null
     * @throws HreflangServiceException
     */
    public function getTags()
    {
        if(! $this->_active) {
            return null;
        }

        $items = $this->getItems();
        if(! $items) {
            return null;
        }

        $tags = [];
        foreach ($items as $hreflang =>  $link) {
            $tags[] = Html::tag('link', null, ['rel' => 'alternate', 'href' => $link, 'hreflang' => $hreflang]);
        }
        if(count($tags) <= 1) {
            return null;
        }

        return implode('', $tags);
    }

    /**
     * Получение элементов для формирования тэгов
     *
     * @return $array
     */
    private function getItems()
    {
        $domainMap = Yii::$app->params['yii2handbook']['domainMap'] ?? null;
        if(! $domainMap) {
            throw new HreflangServiceException('domainMap is not defined.');
        }

        $result = [];
        $manager = Yii::$app->urlManager;
        $rule = $manager->getCurrentRule();
        if(! $rule) {
            return $result;
        }

        list($route, $params) = $rule->parseRequest($manager, Yii::$app->getRequest());
        foreach ($domainMap as $domain => $settings) {
            if(
                ! isset($settings['hreflang'])
                || (! $rule->pattern && ! isset($rule->patterns[$settings['alias']]))
            ) {
                continue;
            }

            if(
                is_array($rule->patterns)
                && count($rule->patterns) > 1
            ) {
                $rule->reinit($settings['alias']);
            }

            $schema = $settings['shema'] ?? 'https';
            $result[$settings['hreflang']] =  "{$schema}://{$domain}/{$rule->createUrl($manager, $route, $params)}";
        }

        if($result) {
            $rule->reset();
        }

        return $result;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HreflangServiceException extends \Exception
{

}