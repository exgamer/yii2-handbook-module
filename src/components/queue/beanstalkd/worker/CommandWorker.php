<?php

namespace concepture\yii2handbook\components\queue\beanstalkd\worker;

use concepture\yii2handbook\components\queue\beanstalkd\BaseTubeEnum;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Воркер для запуска консольных команд
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CommandWorker extends BaseWorker
{
    /**
     * @inheritDoc
     */
    public static function getTubeName()
    {
        return BaseTubeEnum::COMMAND;
    }

    /**
     * @inheritDoc
     */
    public function execute($data)
    {
        $command = $data['command'];
        if (! is_array($command)){
            $command = [$command];
        }

        $result = [];
        foreach ($command as $index => $part){
            if ($index == 0){
                $result[$index] = 'php yii ' .$part;
                continue;
            }
            $result[$index] = $part;
        }
        if (isset($data['alias'])){
            $result[] = '--alias=' . $data['alias'];
        }

        $process  = Process::fromShellCommandline(implode(' ', $result));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}