<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Command\Factory;

use CodeTool\ArtifactDownloader\CmdRunner\CmdRunnerInterface;
use CodeTool\ArtifactDownloader\CmdRunner\Command\CmdRunnerCommandExec;

class CmdRunnerCommandFactory implements CmdRunnerCommandFactoryInterface
{
    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @param CmdRunnerInterface $cmdRunner
     */
    public function __construct(CmdRunnerInterface $cmdRunner)
    {
        $this->cmdRunner = $cmdRunner;
    }

    /**
     * @param bool     $clearEnv
     * @param string[] $env
     *
     * @return string[]
     */
    private function getExecEnv($clearEnv = false, array $env = [])
    {
        $result = [];
        if (false === $clearEnv) {
            $result = $_ENV;
        }

        return array_merge($result, $env);
    }

    /**
     * @param string   $cmd
     * @param string   $cwd
     * @param bool     $clearEnv
     * @param string[] $env
     *
     * @return CmdRunnerCommandExec
     */
    public function createExec($cmd, $cwd, $clearEnv = false, array $env = [])
    {
        return new CmdRunnerCommandExec($this->cmdRunner, $cmd, $cwd, $this->getExecEnv($clearEnv, $env));
    }
}
