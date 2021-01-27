<?php

namespace Lib;

use Swoole\Server;

/**
 * 无法通过reload重新读取此文件内容
 * Class SwooleManager
 * @package Lib
 */
class SwooleManager
{
    public function onManagerStart(Server $server)
    {
        swoole_set_process_name('swoole_manager');
    }

    public function onWorkerError(Server $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {
        $msg = json_encode([
            'worker_id' => $workerId,
            'worker_pid' => $workerPid,
            'exit_code' => $exitCode,
            'signal' => $signal
        ]);
        echo(date('Y-m-d H:i:s') . ':' . $msg . "\n");
    }
}