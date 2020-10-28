<?php

namespace concepture\yii2handbook\components\i18n;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Обработчик копирования
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SourceMessageCopyHandler extends BaseObject
{
    /**
     * @var string
     */
    public $db = 'db';
    /**
     * @var string
     */
    public $sourceMessageTable = 'source_message';
    /**
     * @var string
     */
    public $messageTable = 'message';

    /**
     * @inheritDoc
     */
    public function init()
    {
        if(! $this->db) {
            throw new InvalidConfigException('Property `db` must be set');
        }

        if(! $this->sourceMessageTable) {
            throw new InvalidConfigException('Property `sourceMessageTable` must be set');
        }

        if(! $this->messageTable) {
            throw new InvalidConfigException('Property `sourceMessageTable` must be set');
        }
    }

    /**
     * Запуск обработчика
     *
     * @param string $from
     * @param string $to
     * @param array $items
     *
     * @return array
     */
    public function __invoke(string $from, string $to, array $items) : array
    {
        $connection = Yii::$app->{$this->db};

        return $connection->transaction(function() use($from, $to, $items, $connection) {
            $result = [];
            $query = Yii::createObject(Query::class);
            $query
                ->from($this->sourceMessageTable)
                ->where([
                    'category' => $from,
                    'message' => $items
                ]);

            $sourceMessages = $query->all($connection);

            $rows = [];
            if($sourceMessages) {
                foreach ($sourceMessages as $sourceMessage) {
                    $query
                        ->from($this->sourceMessageTable)
                        ->where([
                            'category' => $to,
                            'message' => $sourceMessage['message']
                        ]);

                    if($query->exists($connection)) {
                        throw new \Exception("Source message `{$sourceMessage['message']}` with category `{$sourceMessage['category']}` already exists");
                    }

                    unset($sourceMessage['id']);
                    $sourceMessage['category'] = $to;
                    $rows[] = $sourceMessage;
                }

                $command = $connection->createCommand()->batchInsert($this->sourceMessageTable, array_keys($sourceMessage), $rows);
                $rawSql = $command->rawSql;
                $command->execute();
                $result[] = $rawSql;

                $query = Yii::createObject(Query::class);
                $query
                    ->from('source_message')
                    ->where([
                        'category' => $to,
                        'message' => $items
                    ])
                    ->indexBy('message');

                $copySourceMessages = $query->all($connection);
                if($sourceMessages && $copySourceMessages) {
                    foreach ($sourceMessages as $sourceMessage) {
                        $copySourceMessage = $copySourceMessages[$sourceMessage['message']] ?? null;
                        if(! $copySourceMessages) {
                            throw new \Exception("Copy source message `{$sourceMessage['message']}` is not found");
                        }

                        $query = Yii::createObject(Query::class);
                        $query
                            ->from('message')
                            ->where(['id' => $sourceMessage['id']]);

                        $messages = $query->all($connection);
                        if(! $messages) {
                            throw new \Exception("Messages for `{$sourceMessage['message']}` is empty");
                        }

                        $rows = [];
                        foreach ($messages as $message) {
                            $message['id'] = $copySourceMessage['id'];
                            $rows[] = $message;
                        }

                        $command = $connection->createCommand()->batchInsert($this->messageTable, array_keys($message), $rows);
                        $rawSql = $command->rawSql;
                        $command->execute();
                        $result[] = $rawSql;
                    }
                }
            }

            return $result;
        });
    }
}