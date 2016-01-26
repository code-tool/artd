<?php

namespace CodeTool\ArtifactDownloader\CmdRunner;

use CodeTool\ArtifactDownloader\CmdRunner\Result\CmdRunnerResultInterface;
use CodeTool\ArtifactDownloader\CmdRunner\Result\Factory\CmdRunnerResultFactoryInterface;

class CmdRunner implements CmdRunnerInterface
{
    /**
     * @var CmdRunnerResultFactoryInterface
     */
    private $cmdRunnerResultFactory;

    /**
     * @param CmdRunnerResultFactoryInterface $cmdRunnerResultFactory
     */
    public function __construct(CmdRunnerResultFactoryInterface $cmdRunnerResultFactory)
    {
        $this->cmdRunnerResultFactory = $cmdRunnerResultFactory;
    }

    /**
     * @param string        $cmd
     * @param string|null   $cwd
     * @param string[]|null $env
     *
     * @return CmdRunnerResultInterface
     */
    public function run($cmd, $cwd = null, $env = null)
    {
        $descriptorSpec = [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = @proc_open($cmd, $descriptorSpec, $pipes, $cwd, $env);
        if (false === is_resource($process)) {
            return $this->cmdRunnerResultFactory->makeFromGetLast();
        }

        $stdOut = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stdError = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return $this->cmdRunnerResultFactory->make($exitCode, $stdOut, $stdError);
    }
}
