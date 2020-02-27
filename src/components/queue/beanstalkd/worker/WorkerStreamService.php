<?php

namespace concepture\yii2handbook\components\queue\beanstalkd\worker;

use yii\helpers\Json;
use yii\caching\CacheInterface;
use Symfony\Component\Process\Process;
use Ramsey\Uuid\Uuid;

/**
 * Сервис для работы воркера через отдельный поток
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class WorkerStreamService
{
    /**
     * @var CacheInterface
     */
    private $_storage;

    /**
     * Конструктор
     *
     * @param CacheInterface $storage
     * @param array $config
     */
    function __construct(CacheInterface $storage)
    {
        $this->_storage = $storage;
        $this->_storage->keyPrefix = 'stream-workers:';
    }

    /**
     * @return CacheInterface
     */
    protected function getStorage()
    {
        return $this->_storage;
    }

    /**
     * Возвращает ключ хранилища
     *
     * @return string
     * @throws \Exception
     */
    protected function getKey()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Установка полезной нагрузки в хранилище
     *
     * @param string $key
     * @param array $payload
     *
     * @return bool
     */
    protected function setData(string $key, array $payload)
    {
        $data = Json::encode($payload);
        $result = $this->getStorage()->set($key, $data);
        if(! $result) {
            throw new WorkerStreamServiceException("failed to set key {$key}, with data: {$data}");
        }

        return true;
    }

    /**
     * Получение данных из хранилища по ключу
     *
     * @param string|null $key
     */
    public function getData(string $key)
    {
        if(! $this->getStorage()->exists($key)) {
            return null;
        }

        return $this->getStorage()->get($key);
    }

    /**
     * Удаление записи в хранилище по ключу
     *
     * @param string|null $key
     */
    public function removeData(string $key)
    {
        if(! $this->getStorage()->exists($key)) {
            return null;
        }

        return $this->getStorage()->delete($key);
    }

    /**
     * Выполнение задачи
     *
     * @param string $command
     * @param array $options
     * @throws WorkerStreamServiceException
     *
     * @return string|null
     */
    public function run(string $command, array $payload)
    {
        $alias = $payload['alias'] ?? null;
        if(! $alias) {
            throw new WorkerStreamServiceException('`alias` is not found in payload.');
        }

        $storageKey = $this->getKey();
        $this->setData($storageKey, $payload);
        $options = [
            'php',
            'yii',
            $command . '/stream-execute',
            "{$storageKey}",
            "--alias={$payload['alias']}"
        ];
        $command = implode(' ', $options);
        $process = Process::fromShellCommandline($command);
        $result = [
            'status' => true,
            'message' => "Command: {$command} \n"
        ];

        $process->run( function ($type, $buffer) use (&$result) {
            if (Process::ERR === $type) {
                $result['status'] = false;
            }

            $result['message'] .= trim($buffer);
        });

        if(isset($result['status']) && ! $result['status']) {
            throw new WorkerException("Error: {$result['message']}");
        }

        return $result['message'] ?? true;
    }
}

/**
 * Исключение сервиса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class WorkerStreamServiceException extends \Exception
{

}