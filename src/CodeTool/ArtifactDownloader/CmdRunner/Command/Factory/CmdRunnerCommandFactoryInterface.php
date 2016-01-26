<?php

namespace CodeTool\ArtifactDownloader\CmdRunner\Command\Factory;

use CodeTool\ArtifactDownloader\CmdRunner\Command\CmdRunnerCommandExec;

interface CmdRunnerCommandFactoryInterface
{
    /**
     * @param string   $cmd
     * @param string   $cwd
     * @param bool     $clearEnv
     * @param string[] $env
     *
     * @return CmdRunnerCommandExec
     */
    public function createExecCommand($cmd, $cwd, $clearEnv = false, array $env = []);
}
